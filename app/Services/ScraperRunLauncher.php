<?php

namespace App\Services;

use App\Http\Controllers\Admin\ScraperController;
use App\Models\ScraperRun;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScraperRunLauncher
{
    public function launch(ScraperRun $run): void
    {
        $sources = ScraperController::SOURCES;
        $signature = $sources[$run->source]['command'] ?? null;
        if (! $signature) return;

        if (! $run->log_path) {
            $logDir = storage_path('logs/scrapers');
            if (! File::isDirectory($logDir)) {
                File::makeDirectory($logDir, 0775, true);
            }
            $run->log_path = $logDir . DIRECTORY_SEPARATOR . 'run-' . $run->id . '.log';
            $run->save();
        }

        $this->spawnBackgroundScraper($signature, $run->city_slug, (int) $run->requested_count, $run->id, $run->log_path);
    }

    /**
     * If nothing is currently running for the given source, promote the
     * oldest pending run to running by spawning it. Called from the command's
     * completion hook to chain multi-city batches.
     */
    public function launchNextPendingIfIdle(string $source): ?ScraperRun
    {
        $active = ScraperRun::where('source', $source)
            ->where('status', ScraperRun::STATUS_RUNNING)
            ->exists();
        if ($active) return null;

        $next = ScraperRun::where('source', $source)
            ->where('status', ScraperRun::STATUS_PENDING)
            ->orderBy('id')
            ->first();
        if (! $next) return null;

        $this->launch($next);
        return $next;
    }

    private function spawnBackgroundScraper(string $signature, string $city, int $limit, int $runId, string $logPath): void
    {
        $phpBinary = PHP_BINARY;
        $artisan = base_path('artisan');

        // Manual admin runs always require a phone. Only append the flag on
        // commands whose signature accepts it — Ivy Societe hardcodes the
        // phone requirement in its importer and would reject an unknown opt.
        $extraArgs = $signature === 'scrape:massagerepublic' ? ' --require-phone' : '';

        if (Str::startsWith(strtoupper(PHP_OS), 'WIN')) {
            $cmd = sprintf(
                'start /B "" %s %s %s --city=%s --limit=%d --run-id=%d%s > %s 2>&1',
                escapeshellarg($phpBinary),
                escapeshellarg($artisan),
                escapeshellarg($signature),
                escapeshellarg($city),
                $limit,
                $runId,
                $extraArgs,
                escapeshellarg($logPath)
            );
            pclose(popen($cmd, 'r'));
        } else {
            $cmd = sprintf(
                'nohup %s %s %s --city=%s --limit=%d --run-id=%d%s > %s 2>&1 &',
                escapeshellarg($phpBinary),
                escapeshellarg($artisan),
                escapeshellarg($signature),
                escapeshellarg($city),
                $limit,
                $runId,
                $extraArgs,
                escapeshellarg($logPath)
            );
            exec($cmd);
        }
    }
}
