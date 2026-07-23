<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Spawns the Node/Playwright phone-reveal worker for one or more profile slugs
 * and returns the resulting phone strings keyed by slug.
 *
 *   $worker  = new MassageRepublicPhoneWorker();
 *   $phones  = $worker->reveal(['tina-indian-independent-high-profile', '...']);
 *   //   ['tina-indian-independent-high-profile' => '+918655033983', ... ]
 */
class MassageRepublicPhoneWorker
{
    protected string $nodeBinary;
    protected string $workerDir;
    protected string $workerScript;
    protected int $timeoutSecondsPerSlug;

    /** @var array<string,?string>  slug → last error string (null when reveal succeeded) */
    protected array $lastErrors = [];

    /** @var array<string,array{whatsapp:bool,telegram:bool,signal:bool,wechat:bool}>  slug → app presence flags from the reveal modal */
    protected array $lastApps = [];

    /**
     * Batched-reveal cache. Filled by preheat() so subsequent revealOne()
     * calls don't have to spawn a fresh Node/Chromium/login per slug —
     * which was hammering MR into Cloudflare-block territory.
     *
     * @var array<string,?string>  slug → phone (or null if reveal failed)
     */
    protected array $cache = [];
    /** @var array<string,bool>  slug present in cache (so null means "we tried and failed") */
    protected array $cacheHit = [];

    public function __construct()
    {
        $this->nodeBinary = env('NODE_BINARY', 'node');
        $this->workerDir = base_path('tools/mr-phone-worker');
        $this->workerScript = 'worker.js';
        $this->timeoutSecondsPerSlug = (int) env('MASSAGE_REPUBLIC_PHONE_TIMEOUT', 120);
    }

    public function isAvailable(): bool
    {
        return file_exists($this->workerDir . DIRECTORY_SEPARATOR . $this->workerScript);
    }

    /** Reason the last reveal for $slug failed, or null when it succeeded / wasn't attempted. */
    public function getLastError(string $slug): ?string
    {
        return $this->lastErrors[$slug] ?? null;
    }

    /**
     * Messaging-app presence detected in the MR reveal modal for $slug.
     * Returns ['whatsapp'=>bool,'telegram'=>bool,'signal'=>bool,'wechat'=>bool]
     * (all false if reveal failed or apps weren't reported).
     *
     * @return array{whatsapp:bool,telegram:bool,signal:bool,wechat:bool}
     */
    public function getLastApps(string $slug): array
    {
        return $this->lastApps[$slug] ?? [
            'whatsapp' => false, 'telegram' => false, 'signal' => false, 'wechat' => false,
        ];
    }

    /**
     * @param string[] $slugs
     * @return array<string,?string>  slug → phone (null if reveal failed for that slug)
     */
    public function reveal(array $slugs, string $listingPath = '/female-escorts-in-dubai'): array
    {
        $slugs = array_values(array_filter(array_unique($slugs)));
        if (empty($slugs)) {
            return [];
        }
        // Reset and pre-fill errors so callers can always introspect a reason.
        foreach ($slugs as $s) {
            $this->lastErrors[$s] = null;
            $this->lastApps[$s] = ['whatsapp' => false, 'telegram' => false, 'signal' => false, 'wechat' => false];
        }

        if (! $this->isAvailable()) {
            foreach ($slugs as $s) {
                $this->lastErrors[$s] = 'worker script missing (tools/mr-phone-worker/worker.js)';
            }
            return [];
        }

        $username = env('MASSAGE_REPUBLIC_USERNAME');
        $password = env('MASSAGE_REPUBLIC_PASSWORD');
        if (! $username || ! $password) {
            Log::warning('MR phone worker: missing MR credentials');
            foreach ($slugs as $s) {
                $this->lastErrors[$s] = 'missing MASSAGE_REPUBLIC_USERNAME/PASSWORD in .env';
            }
            return [];
        }

        // The Playwright worker should hit the Cloudflare-free mirror
        // (massagerepublic.tk) because .com serves an interstitial to
        // headless browsers and the login form is never interactable.
        // The scraper's CURL-based login, on the other hand, works fine
        // against .com — so we let them target different hosts via
        // MASSAGE_REPUBLIC_HOST_WORKER (worker-only override); if that
        // isn't set we fall back to MASSAGE_REPUBLIC_HOST, then the .tk
        // mirror as final default.
        $baseHost = trim(env('MASSAGE_REPUBLIC_HOST_WORKER')
            ?: env('MASSAGE_REPUBLIC_HOST')
            ?: 'massagerepublic.tk');
        $config = [
            'username' => $username,
            'password' => $password,
            'listingPath' => '/' . trim($listingPath, '/'),
            'slugs' => $slugs,
            'headless' => true,
            'baseHost' => $baseHost,
        ];

        $hostIp = env('MASSAGE_REPUBLIC_HOST_WORKER_IP')
            ?: env('MASSAGE_REPUBLIC_HOST_IP');
        if ($hostIp) {
            $firstIp = explode(',', $hostIp)[0];
            $config['hostResolve'] = $baseHost . ' ' . trim($firstIp);
        }

        $configPath = $this->writeTempConfig($config);

        try {
            $process = new Process(
                [$this->nodeBinary, $this->workerScript, '--config', $configPath],
                $this->workerDir,
                null,
                null,
                $this->timeoutSecondsPerSlug * count($slugs)
            );

            $results = [];
            foreach ($slugs as $s) {
                $results[$s] = null;
            }

            $sawSlugLine = [];
            $processTimedOut = false;
            try {
                $process->run(function ($type, $buffer) use (&$results, &$sawSlugLine) {
                if ($type !== Process::OUT) {
                    return;
                }
                foreach (preg_split("/\r?\n/", $buffer) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $row = json_decode($line, true);
                    if (! is_array($row)) continue;
                    if (empty($row['slug'])) {
                        // Fatal-level worker error (e.g. login failed) — apply to all slugs.
                        if (! empty($row['error'])) {
                            foreach ($this->lastErrors as $s => $_) {
                                $this->lastErrors[$s] = 'worker: ' . $row['error'];
                            }
                        }
                        continue;
                    }
                    $sawSlugLine[$row['slug']] = true;
                    if (is_array($row['apps'] ?? null)) {
                        $this->lastApps[$row['slug']] = [
                            'whatsapp' => (bool) ($row['apps']['whatsapp'] ?? false),
                            'telegram' => (bool) ($row['apps']['telegram'] ?? false),
                            'signal'   => (bool) ($row['apps']['signal']   ?? false),
                            'wechat'   => (bool) ($row['apps']['wechat']   ?? false),
                        ];
                    }
                    // Diagnostic dump so we can see MR's actual reveal-modal markup
                    // and tune selectors against it. Logged at info level — feel free
                    // to remove this block once selectors are fingerprinted.
                    if (! empty($row['modalHtml'])) {
                        Log::info('MR phone worker: reveal-modal HTML', [
                            'slug' => $row['slug'],
                            'html' => $row['modalHtml'],
                        ]);
                    }
                    if (! empty($row['ok']) && ! empty($row['phone'])) {
                        $results[$row['slug']] = (string) $row['phone'];
                        $this->lastErrors[$row['slug']] = null;
                    } else {
                        $err = $row['error'] ?? 'unknown';
                        $this->lastErrors[$row['slug']] = (string) $err;
                        Log::info('MR phone worker: slug failed', [
                            'slug' => $row['slug'],
                            'error' => $err,
                        ]);
                    }
                }
            });
            } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException $timeout) {
                // Symfony killed the worker for exceeding the per-batch
                // wall clock. Don't rethrow — the batch is a diagnostic
                // best-effort; mark all not-yet-emitted slugs with a
                // clear reason so --require-phone can skip them
                // cleanly instead of aborting the whole scrape run.
                $processTimedOut = true;
                Log::warning('MR phone worker: batch timed out', [
                    'timeout_s' => $this->timeoutSecondsPerSlug * count($slugs),
                    'slugs_emitted' => count($sawSlugLine),
                    'slugs_total' => count($slugs),
                ]);
            }

            // Always capture stderr — the worker writes step-by-step progress
            // there ("[step … login: goto /sign-in") so we can pinpoint
            // exactly where a hung batch stalled even when the process ran
            // to Symfony's process timeout without emitting any per-slug
            // JSON on stdout.
            $stderrFull = $process->getErrorOutput();
            if ($stderrFull !== '') {
                Log::info('MR phone worker stderr', ['stderr' => substr($stderrFull, -4000)]);
            }

            if ($processTimedOut) {
                // Pull last handful of step lines so caller sees where it stalled.
                $lastSteps = [];
                foreach (array_reverse(preg_split("/\r?\n/", $stderrFull) ?: []) as $line) {
                    if (str_starts_with($line, '[step ')) {
                        $lastSteps[] = $line;
                        if (count($lastSteps) >= 3) break;
                    }
                }
                $stallHint = $lastSteps ? implode(' | ', array_reverse($lastSteps)) : 'no step trace';
                foreach (array_keys($this->lastErrors) as $s) {
                    if (empty($sawSlugLine[$s]) && $this->lastErrors[$s] === null) {
                        $this->lastErrors[$s] = 'worker timed out at: ' . $stallHint;
                    }
                }
            } elseif (! $process->isSuccessful()) {
                $stderrTail = substr($stderrFull, -500);
                Log::warning('MR phone worker: non-zero exit', [
                    'exit_code' => $process->getExitCode(),
                    'stderr' => $stderrTail,
                ]);
                foreach (array_keys($this->lastErrors) as $s) {
                    if (empty($sawSlugLine[$s]) && $this->lastErrors[$s] === null) {
                        $this->lastErrors[$s] = 'worker exited ' . $process->getExitCode() . ': ' . trim($stderrTail);
                    }
                }
            }

            return $results;
        } finally {
            @unlink($configPath);
        }
    }

    public function revealOne(string $slug, string $listingPath = '/female-escorts-in-dubai'): ?string
    {
        // Pre-warmed by preheat() — one shared browser session for the
        // whole batch, so we don't hit MR/Cloudflare N times per city.
        if (isset($this->cacheHit[$slug])) {
            return $this->cache[$slug] ?? null;
        }
        $result = $this->reveal([$slug], $listingPath);
        return $result[$slug] ?? null;
    }

    /**
     * Batch-reveal phones for a list of slugs in a single worker session
     * and cache the results. Call this once at the top of a scrape run;
     * subsequent revealOne() calls will hit the cache.
     *
     * @param string[] $slugs
     */
    public function preheat(array $slugs, string $listingPath = '/female-escorts-in-dubai'): void
    {
        $slugs = array_values(array_filter(array_unique($slugs)));
        // Skip anything already cached from a previous preheat call.
        $slugs = array_values(array_filter($slugs, fn ($s) => ! isset($this->cacheHit[$s])));
        if (empty($slugs)) {
            return;
        }
        $results = $this->reveal($slugs, $listingPath);
        foreach ($slugs as $s) {
            $this->cache[$s] = $results[$s] ?? null;
            $this->cacheHit[$s] = true;
        }
    }

    protected function writeTempConfig(array $config): string
    {
        $path = tempnam(sys_get_temp_dir(), 'mrphone_') . '.json';
        file_put_contents($path, json_encode($config));
        return $path;
    }
}
