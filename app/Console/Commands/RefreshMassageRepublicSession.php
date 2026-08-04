<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Mint a fresh MR session file by running the Playwright cookie-minter.
 *
 *   php artisan mr:refresh-session
 *
 * What it does:
 *   1. Reads MR creds + host from .env (same values the Guzzle scraper uses).
 *   2. Writes a temp JSON config with those values + the target output path
 *      (storage/app/mr-session.json).
 *   3. Spawns node tools/mr-phone-worker/mint-cookies.js --config <tmp>.
 *   4. The Node script launches headless Chromium, passes the Cloudflare
 *      challenge (from the server's IP, so the cf_clearance it mints is
 *      valid for the server's IP), logs into MR, extracts cookies, and
 *      writes them to storage/app/mr-session.json in the Cookie-Editor
 *      format MassageRepublicScraper::loadSessionFile() already accepts.
 *   5. Reports whether cf_clearance + _session_id landed.
 *
 * Intended cadence: run every few hours via cron, or just before any
 * scrape:massagerepublic run. cf_clearance typically stays valid 30 min
 * to a few hours, _session_id ~3 days.
 */
class RefreshMassageRepublicSession extends Command
{
    protected $signature = 'mr:refresh-session
                            {--visible : Launch Chromium with headless=false (for debugging on a machine with a display)}';

    protected $description = 'Mint a fresh MR session cookie file by running Playwright from the server, so cf_clearance is bound to the server IP.';

    public function handle(): int
    {
        $username = env('MASSAGE_REPUBLIC_USERNAME');
        $password = env('MASSAGE_REPUBLIC_PASSWORD');

        if (! $username || ! $password) {
            $this->error('Missing MASSAGE_REPUBLIC_USERNAME or MASSAGE_REPUBLIC_PASSWORD in .env');
            return self::FAILURE;
        }

        // Match the host the Guzzle scraper is targeting. If they don't match,
        // cf_clearance won't apply because cookies are domain-scoped.
        $baseHost = trim((string) env('MASSAGE_REPUBLIC_HOST', 'massagerepublic.com'));
        $hostResolve = env('MASSAGE_REPUBLIC_CURL_RESOLVE');

        $outputPath = storage_path('app/mr-session.json');
        $workerDir = base_path('tools/mr-phone-worker');
        $script = 'mint-cookies.js';
        $scriptFullPath = $workerDir . DIRECTORY_SEPARATOR . $script;

        if (! is_file($scriptFullPath)) {
            $this->error("Cookie-minter script missing at {$scriptFullPath}");
            return self::FAILURE;
        }

        $nodeBinary = env('NODE_BINARY', 'node');

        // Build the config the Node script expects. Mirrors the shape used by
        // MassageRepublicPhoneWorker so future maintainers see one pattern.
        $config = [
            'username' => $username,
            'password' => $password,
            'baseHost' => $baseHost,
            'outputPath' => $outputPath,
            'headless' => ! $this->option('visible'),
        ];

        // Optional CapSolver escape hatch — kicks in only if patchright can't
        // clear the Cloudflare challenge on its own (~half the time on
        // datacenter IPs). ~$0.001 per solve, so ~$0.36/mo if we mint every
        // 2h. Left unset = fall back to pure patchright and hope for the best.
        $capsolverKey = env('CAPSOLVER_API_KEY');
        if ($capsolverKey) {
            $config['capsolverApiKey'] = $capsolverKey;
            $this->line('CapSolver: enabled (will be used only if patchright fails)');
        } else {
            $this->line('CapSolver: not configured — set CAPSOLVER_API_KEY in .env to enable Turnstile fallback');
        }

        if ($hostResolve) {
            // MASSAGE_REPUBLIC_CURL_RESOLVE format is "host:port:ip" (curl's
            // native format). Chromium's --host-resolver-rules wants "host ip",
            // so strip the port. The mint script accepts either shape but we
            // normalise here to keep its parsing trivial.
            $parts = explode(':', $hostResolve);
            if (count($parts) >= 3) {
                $config['hostResolve'] = $parts[0] . ' ' . $parts[2];
            } else {
                $config['hostResolve'] = $hostResolve;
            }
        }

        $configPath = tempnam(sys_get_temp_dir(), 'mr-mint-');
        file_put_contents($configPath, json_encode($config, JSON_UNESCAPED_SLASHES));

        try {
            $this->line("Node binary:  {$nodeBinary}");
            $this->line("Script:       {$scriptFullPath}");
            $this->line("Target host:  https://{$baseHost}");
            $this->line("Writing to:   {$outputPath}");
            $this->newLine();
            $this->line('Launching Chromium — this can take 60-120s while Cloudflare is solved and login completes…');

            $process = new Process(
                [$nodeBinary, $script, '--config', $configPath],
                $workerDir,
                null,
                null,
                180 // 3-minute overall cap
            );

            $stdout = '';
            $stderr = '';
            $process->run(function ($type, $buffer) use (&$stdout, &$stderr) {
                if ($type === Process::OUT) {
                    $stdout .= $buffer;
                } else {
                    // Stream step lines to the console so operators see progress
                    // rather than staring at a blank terminal for 90s.
                    $this->getOutput()->write("<comment>{$buffer}</comment>");
                    $stderr .= $buffer;
                }
            });

            if (! $process->isSuccessful()) {
                $this->newLine();
                $this->error('Cookie-minter exited with a non-zero status.');
                $this->error("Last stdout line: " . trim(collect(preg_split("/\r?\n/", $stdout))->filter()->last() ?? '(empty)'));
                Log::warning('MR cookie-minter failed', [
                    'exit' => $process->getExitCode(),
                    'stdout' => substr($stdout, -2000),
                    'stderr' => substr($stderr, -2000),
                ]);
                return self::FAILURE;
            }

            // The Node script emits exactly one JSON summary line to stdout.
            // Find it by walking the tail — anything else on stdout is noise.
            $summary = null;
            foreach (array_reverse(preg_split("/\r?\n/", $stdout)) as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $decoded = json_decode($line, true);
                if (is_array($decoded)) {
                    $summary = $decoded;
                    break;
                }
            }

            if (! $summary) {
                $this->error('Cookie-minter succeeded but emitted no JSON summary. Something is off with the script — check the stderr output above.');
                return self::FAILURE;
            }

            $this->newLine();
            if (! ($summary['ok'] ?? false)) {
                $this->error('Cookie-minter reported failure: ' . ($summary['error'] ?? 'unknown'));
                return self::FAILURE;
            }

            $this->info(sprintf(
                'Session file written: %d cookies (cf_clearance: %s, _session_id: %s)',
                (int) ($summary['cookiesWritten'] ?? 0),
                ! empty($summary['hasCfClearance']) ? 'yes' : 'NO',
                ! empty($summary['hasSessionId']) ? 'yes' : 'NO'
            ));
            $this->line("Location: " . ($summary['outputPath'] ?? $outputPath));

            if (empty($summary['hasCfClearance'])) {
                $this->warn('cf_clearance was NOT minted. Cloudflare may have served a challenge that Playwright could not auto-solve (rare). Try `--visible` on a desktop machine to see what happened.');
            }

            $this->newLine();
            $this->line('Verify with: <info>php artisan mr:test-session</info>');
            return self::SUCCESS;
        } finally {
            @unlink($configPath);
        }
    }
}
