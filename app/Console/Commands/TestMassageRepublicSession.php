<?php

namespace App\Console\Commands;

use App\Services\MassageRepublicScraper;
use Illuminate\Console\Command;

/**
 * Quick health check for the MR session-cookie file.
 *
 * Runs the same login + one listing GET the real scraper would do, and prints
 * whether we made it past Cloudflare. Way faster than running a full
 * scrape:massagerepublic just to find out the cookies expired.
 *
 *   php artisan mr:test-session
 *   php artisan mr:test-session --city=toronto
 *
 * Prereq: MASSAGE_REPUBLIC_USE_SESSION_FILE=true in .env AND
 * storage/app/mr-session.json exists with cookies exported from a real,
 * logged-in browser session (Cookie-Editor extension → JSON export).
 */
class TestMassageRepublicSession extends Command
{
    protected $signature = 'mr:test-session
                            {--city= : Override the listing path city slug (default: what MASSAGE_REPUBLIC_LISTING_PATH resolves to)}';

    protected $description = 'Verify the MR session cookie file is still valid by attempting login + one listing fetch.';

    public function handle(): int
    {
        $username = config('services.massagrerepublic.username') ?: env('MASSAGE_REPUBLIC_USERNAME');
        $password = config('services.massagrerepublic.password') ?: env('MASSAGE_REPUBLIC_PASSWORD');

        if (! $username || ! $password) {
            $this->error('Missing MASSAGE_REPUBLIC_USERNAME or MASSAGE_REPUBLIC_PASSWORD in .env');
            return self::FAILURE;
        }

        $useSession = filter_var(env('MASSAGE_REPUBLIC_USE_SESSION_FILE', false), FILTER_VALIDATE_BOOLEAN);
        if (! $useSession) {
            $this->warn('MASSAGE_REPUBLIC_USE_SESSION_FILE is not enabled in .env — the scraper will fall back to Guzzle credential login (Cloudflare will block this).');
        }

        $sessionPath = storage_path('app/mr-session.json');
        $this->line("Session file path: {$sessionPath}");
        $this->line('Session file exists: ' . (is_file($sessionPath) ? 'yes' : 'NO'));

        $scraper = new MassageRepublicScraper($username, $password);

        $this->line('Session seeded into cookie jar: ' . ($scraper->isSessionSeeded() ? 'yes' : 'NO'));

        // Dump the cookie jar so we can see whether cf_clearance / _session_id
        // actually landed. The 403+challenge is often "loaded cookies but not
        // the right ones" rather than "loader broken".
        $cookies = $scraper->describeCookies();
        $this->line('Cookies loaded: ' . count($cookies));
        $hasCfClearance = false;
        $hasSessionId = false;
        foreach ($cookies as $c) {
            $this->line(sprintf(
                '  • %-25s  domain=%-30s  expires=%-25s  len=%d',
                $c['name'],
                $c['domain'],
                $c['expires_iso'],
                $c['value_len']
            ));
            if ($c['name'] === 'cf_clearance') $hasCfClearance = true;
            if ($c['name'] === '_session_id') $hasSessionId = true;
        }
        if (! $hasCfClearance) {
            $this->warn('  ⚠  cf_clearance is MISSING from the session file. Cloudflare will always challenge without it.');
        }
        if (! $hasSessionId) {
            $this->warn('  ⚠  _session_id is MISSING from the session file. Even if CF clears, MR will treat you as logged out.');
        }

        $this->line('Guzzle User-Agent: ' . $scraper->getUserAgent());
        $this->line('  ↳ cf_clearance is bound to the UA that solved the challenge. If your browser used a different UA than the string above, CF will reject.');

        $this->line('Attempting login…');
        $ok = $scraper->attemptLogin();
        if (! $ok) {
            $this->error('Login FAILED: ' . ($scraper->getLastLoginError() ?? 'unknown reason'));
            return self::FAILURE;
        }
        $this->info('Login OK.');

        $this->line('Probing listing page to confirm cookies actually reach an authenticated response…');
        $probe = $scraper->probeListing();

        $this->line("  URL:    {$probe['url']}");
        $this->line("  HTTP:   {$probe['status']}");

        if (! $probe['ok']) {
            $snippet = trim(substr(strip_tags($probe['body']), 0, 300));
            $this->error('Probe FAILED.');
            if ($probe['error']) {
                $this->error("  error: {$probe['error']}");
            }
            if (str_contains($probe['body'], 'Just a moment')) {
                $this->error('  Cloudflare challenge detected — cookies are missing or expired. Re-export cf_clearance + _session_id from a real browser.');
            } else {
                $this->error("  body:  {$snippet}");
            }
            return self::FAILURE;
        }

        $this->info('Probe OK — Cloudflare cleared, listing page fetched cleanly. Session is healthy.');
        return self::SUCCESS;
    }
}
