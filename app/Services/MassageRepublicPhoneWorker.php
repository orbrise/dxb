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

    /**
     * @param string[] $slugs
     * @return array<string,?string>  slug → phone (null if reveal failed for that slug)
     */
    public function reveal(array $slugs, string $listingPath = '/female-escorts-in-dubai'): array
    {
        $slugs = array_values(array_filter(array_unique($slugs)));
        if (empty($slugs) || ! $this->isAvailable()) {
            return [];
        }

        $username = env('MASSAGE_REPUBLIC_USERNAME');
        $password = env('MASSAGE_REPUBLIC_PASSWORD');
        if (! $username || ! $password) {
            Log::warning('MR phone worker: missing MR credentials');
            return [];
        }

        $config = [
            'username' => $username,
            'password' => $password,
            'listingPath' => '/' . trim($listingPath, '/'),
            'slugs' => $slugs,
            'headless' => true,
        ];

        $hostIp = env('MASSAGE_REPUBLIC_HOST_IP');
        $baseHost = env('MASSAGE_REPUBLIC_HOST', 'massagerepublic.com');
        if ($hostIp) {
            $firstIp = explode(',', $hostIp)[0];
            $config['hostResolve'] = trim($baseHost) . ' ' . trim($firstIp);
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

            $process->run(function ($type, $buffer) use (&$results) {
                if ($type !== Process::OUT) {
                    return;
                }
                foreach (preg_split("/\r?\n/", $buffer) as $line) {
                    $line = trim($line);
                    if ($line === '') continue;
                    $row = json_decode($line, true);
                    if (! is_array($row) || empty($row['slug'])) continue;
                    if (! empty($row['ok']) && ! empty($row['phone'])) {
                        $results[$row['slug']] = (string) $row['phone'];
                    } else {
                        Log::info('MR phone worker: slug failed', [
                            'slug' => $row['slug'],
                            'error' => $row['error'] ?? 'unknown',
                        ]);
                    }
                }
            });

            if (! $process->isSuccessful()) {
                Log::warning('MR phone worker: non-zero exit', [
                    'exit_code' => $process->getExitCode(),
                    'stderr' => substr($process->getErrorOutput(), 0, 500),
                ]);
            }

            return $results;
        } finally {
            @unlink($configPath);
        }
    }

    public function revealOne(string $slug, string $listingPath = '/female-escorts-in-dubai'): ?string
    {
        $result = $this->reveal([$slug], $listingPath);
        return $result[$slug] ?? null;
    }

    protected function writeTempConfig(array $config): string
    {
        $path = tempnam(sys_get_temp_dir(), 'mrphone_') . '.json';
        file_put_contents($path, json_encode($config));
        return $path;
    }
}
