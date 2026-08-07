<?php

namespace App\Console\Commands;

use App\Services\MassageRepublicPhoneWorker;
use App\Services\MassageRepublicScraper;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Scrape MR from the LOCAL machine (residential IP, CF-trusted) and either
 * write results to a JSON file for manual upload, or POST them straight
 * to the production /api/admin/mr/import endpoint.
 *
 * Why this exists: production cPanel IP is a datacenter IP that Cloudflare
 * challenges + Bright Data has blocklisted massagerepublic.com. Running the
 * scraper from a residential IP (the user's home Windows/laragon box) both
 * sidesteps CF and doesn't need any third-party proxy service.
 *
 * Two-step flow:
 *   1. On the local machine:
 *        php artisan scrape:massagerepublic:local --city=toronto --limit=20
 *      → writes storage/app/mr-scrapes/toronto-2026-08-06_143012.json
 *
 *   2. Either upload manually or add --upload to POST straight to prod:
 *        php artisan scrape:massagerepublic:local --city=toronto --limit=20 \
 *            --upload --upload-url=https://evoory.com/api/admin/mr/import \
 *            --upload-token="$MR_IMPORT_TOKEN"
 *
 * Phone reveal (--no-phone to skip) happens HERE on the local machine because
 * production doesn't have Chromium. The revealed phone rides along in the
 * JSON payload and lands in the massage_republic_profiles.phone column on
 * production without needing the phone-worker there.
 */
class ScrapeMassageRepublicLocal extends Command
{
    protected $signature = 'scrape:massagerepublic:local
                            {--city= : MR city slug, e.g. dubai, toronto, london}
                            {--limit=50 : Max profiles to fetch}
                            {--no-phone : Skip local Playwright phone-reveal step}
                            {--output= : Override output JSON path (default storage/app/mr-scrapes/CITY-TIMESTAMP.json)}
                            {--upload : POST the JSON to production after scraping}
                            {--upload-url= : Production endpoint (default APP_URL/api/admin/mr/import)}
                            {--upload-token= : Bearer token for the import endpoint (default MR_IMPORT_TOKEN env)}';

    protected $description = 'Scrape MR from this machine\'s residential IP and dump profiles to JSON (optionally upload to production).';

    public function handle(MassageRepublicPhoneWorker $phoneWorker): int
    {
        $username = config('services.massagrerepublic.username') ?: env('MASSAGE_REPUBLIC_USERNAME');
        $password = config('services.massagrerepublic.password') ?: env('MASSAGE_REPUBLIC_PASSWORD');

        if (! $username || ! $password) {
            $this->error('Missing MASSAGE_REPUBLIC_USERNAME or MASSAGE_REPUBLIC_PASSWORD in .env');
            return self::FAILURE;
        }

        $citySlug = Str::lower(trim((string) $this->option('city')));
        if ($citySlug === '') {
            $this->error('Pass --city=<slug>, e.g. --city=dubai');
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit') ?: 50;
        $revealPhones = ! $this->option('no-phone');

        $this->info("Scraping {$citySlug} (limit {$limit}) from local IP…");

        $scraper = new MassageRepublicScraper($username, $password);

        // Log transport mode so the operator knows whether they're going direct
        // (residential IP, expected) or accidentally through a proxy.
        if ($scraper->isBrightDataApiEnabled()) {
            $this->warn('Bright Data API mode is ACTIVE — this defeats the purpose of running locally. Unset MASSAGE_REPUBLIC_BRIGHTDATA_API_KEY in .env.');
        } elseif ($scraper->isProxyEnabled()) {
            $this->warn('Bright Data proxy mode is ACTIVE — this defeats the purpose of running locally. Unset MASSAGE_REPUBLIC_PROXY_URL in .env.');
        } else {
            $this->line('Transport: direct (residential IP)');
        }

        try {
            // Local mode never dedupes against the LOCAL DB — the production DB
            // is the source of truth for "already imported". Skip logic runs
            // on the receiving end in MrImportController.
            $profiles = $scraper->scrape($limit, $citySlug, null);
        } catch (\Throwable $e) {
            $this->error('Scraper error: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (empty($profiles)) {
            $this->warn('No profiles scraped. Verify credentials + city slug + that Cloudflare didn\'t challenge this IP.');
            return self::SUCCESS;
        }

        $this->info('Scraped ' . count($profiles) . ' profile(s).');

        // Batch-reveal phones in a single Chromium session — same trick the
        // production command uses. Without this, MR/CF throttles rapid solo
        // phone reveals and everything comes back NULL.
        $phoneCache = [];
        if ($revealPhones) {
            if (! $phoneWorker->isAvailable()) {
                $this->warn('Phone-worker script missing (tools/mr-phone-worker/worker.js) — skipping phone reveal.');
            } else {
                $slugs = array_values(array_filter(array_map(fn($p) => $p['external_id'] ?? null, $profiles)));
                if (! empty($slugs)) {
                    $this->line('Batch-revealing phones for ' . count($slugs) . ' profile(s)…');
                    $listingPath = '/female-escorts-in-' . ltrim($citySlug, '/');
                    $phoneWorker->preheat($slugs, $listingPath);

                    foreach ($slugs as $slug) {
                        $revealed = $phoneWorker->revealOne($slug, $listingPath);
                        if ($revealed) {
                            $phoneCache[$slug] = [
                                'phone' => $revealed,
                                'apps' => method_exists($phoneWorker, 'getLastApps')
                                    ? $phoneWorker->getLastApps($slug)
                                    : [],
                            ];
                        }
                    }
                    $this->line('Revealed ' . count($phoneCache) . ' phone(s).');
                }
            }
        }

        // Merge phones into the profile array so the payload is self-contained.
        // The importer on production reads $profile['phone'] and $profile['apps']
        // directly — no phone worker needed on the receiving end.
        foreach ($profiles as &$p) {
            $ext = $p['external_id'] ?? null;
            if ($ext && isset($phoneCache[$ext])) {
                $p['phone'] = $phoneCache[$ext]['phone'];
                $p['apps'] = $phoneCache[$ext]['apps'];
            }
        }
        unset($p);

        // Assemble the payload. `city_slug` is separate from per-profile `city`
        // because the profile's `city` field is what MR displayed (human text),
        // while `city_slug` is the URL slug we scraped and needs to be used
        // for cities.id resolution on the import side.
        $payload = [
            'city_slug' => $citySlug,
            'scraped_at' => now()->toIso8601String(),
            'scraper_version' => 'local/1',
            'count' => count($profiles),
            'profiles' => $profiles,
        ];

        // Write to disk. Default location groups scrapes by city so a --limit=20
        // run doesn't overwrite a --limit=200 run from the same day.
        $outputPath = trim((string) $this->option('output'));
        if ($outputPath === '') {
            $dir = storage_path('app/mr-scrapes');
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $ts = now()->format('Y-m-d_His');
            $outputPath = "{$dir}/{$citySlug}-{$ts}.json";
        }

        $bytes = file_put_contents($outputPath, json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        if ($bytes === false) {
            $this->error("Failed to write {$outputPath}");
            return self::FAILURE;
        }
        $this->info('Wrote ' . number_format($bytes) . ' bytes → ' . $outputPath);

        // Optional upload. Kept separate from the file write so operators can
        // inspect the JSON before shipping, and so a network hiccup during
        // upload doesn't lose the scrape.
        if ($this->option('upload')) {
            $uploadUrl = trim((string) $this->option('upload-url'))
                ?: (rtrim((string) env('APP_URL', ''), '/') . '/api/admin/mr/import');
            $token = trim((string) $this->option('upload-token'))
                ?: (string) env('MR_IMPORT_TOKEN', '');

            if ($uploadUrl === '' || ! preg_match('#^https?://#i', $uploadUrl)) {
                $this->error('Invalid --upload-url (or APP_URL not set): ' . $uploadUrl);
                return self::FAILURE;
            }
            if ($token === '') {
                $this->error('Missing --upload-token or MR_IMPORT_TOKEN env — refuse to POST without auth.');
                return self::FAILURE;
            }

            $this->line("Uploading to {$uploadUrl}…");
            try {
                $http = new GuzzleClient([
                    'timeout' => 120,
                    'verify' => false,
                    'http_errors' => false,
                ]);
                $res = $http->post($uploadUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ]);
                $status = $res->getStatusCode();
                $body = (string) $res->getBody();
                if ($status >= 200 && $status < 300) {
                    $this->info("Upload OK ({$status}): {$body}");
                } else {
                    $this->error("Upload failed ({$status}): " . substr($body, 0, 500));
                    return self::FAILURE;
                }
            } catch (\Throwable $e) {
                $this->error('Upload threw: ' . $e->getMessage());
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
