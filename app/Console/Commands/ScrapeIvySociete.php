<?php

namespace App\Console\Commands;

use App\Models\IvySocieteProfile;
use App\Models\ScraperRun;
use App\Services\IvySocieteImporter;
use App\Services\IvySocieteScraper;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScrapeIvySociete extends Command
{
    protected $signature = 'scrape:ivysociete
                            {--city=sydney : ivysociete city slug (sydney, london, auckland, etc.)}
                            {--limit=50 : Maximum number of profiles to scrape this run}
                            {--no-import : Scrape into ivysociete_profiles only; skip writing to users/users_profiles}
                            {--run-id= : Internal scraper_runs.id to report progress to (set by the admin UI)}';

    protected $description = 'Scrape ivysociete.com profiles for a given city and import them into users_profiles. Requires a phone number — profiles without one are skipped.';

    public function handle(IvySocieteScraper $scraper, IvySocieteImporter $importer): int
    {
        $run = $this->loadRun();
        $this->markRunRunning($run, 'initializing');

        $citySlug = Str::lower(trim((string) $this->option('city')));
        if ($citySlug === '') {
            $this->error('Pass --city=<slug>, e.g. --city=sydney');
            $this->markRunFailed($run, 'Missing --city');
            return Command::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $importEnabled = ! $this->option('no-import');

        $this->info("ivysociete scraper starting for city '{$citySlug}' (limit={$limit})");
        $this->updateRunProgress($run, 0, $limit, 'scraping listing');

        // Skip profiles we've already imported so re-runs walk further
        // down the listing to find new ones instead of hammering the top
        // of the list. Only applies when importing — --no-import wants
        // to re-scrape everything.
        $skipChecker = null;
        if ($importEnabled) {
            $skipChecker = function (string $externalId): bool {
                return IvySocieteProfile::where('external_id', $externalId)
                    ->whereNotNull('imported_user_id')
                    ->exists();
            };
        }

        try {
            $profiles = $scraper->scrape($limit, $citySlug, $skipChecker);
        } catch (\Throwable $e) {
            $this->error('Scraper error: ' . $e->getMessage());
            $this->markRunFailed($run, 'Scraper error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        if (empty($profiles)) {
            $this->warn("No profiles scraped for '{$citySlug}'. Check the city slug is valid on ivysociete.com.");
            $this->markRunCompleted($run, 'no profiles scraped');
            return Command::SUCCESS;
        }

        $saved = 0;
        $skippedNoPhone = 0;
        $skippedAlreadyImported = 0;
        $cityId = null;

        $totalProfiles = count($profiles);
        $this->updateRunProgress($run, 0, $totalProfiles, 'importing profiles');

        $processed = 0;
        foreach ($profiles as $profileData) {
            $processed++;
            $this->updateRunProgress($run, $processed, $totalProfiles, 'importing profiles');
            // Hard-require phone. User asked to skip profiles without a
            // contactable number — no phone means the row isn't useful
            // for the leads pipeline downstream.
            if (empty($profileData['phone'])) {
                $skippedNoPhone++;
                $this->line("  - {$profileData['external_id']}: skipped (no phone)");
                continue;
            }

            $row = IvySocieteProfile::updateOrCreate(
                ['external_id' => $profileData['external_id']],
                array_merge($profileData, [
                    'scraped_at' => now(),
                    'source_city' => $citySlug,
                ])
            );

            if (! $importEnabled) {
                $saved++;
                continue;
            }

            if ($row->imported_user_id) {
                $skippedAlreadyImported++;
                $this->line("  - {$row->external_id}: already imported (user #{$row->imported_user_id})");
                continue;
            }

            if ($cityId === null) {
                $cityId = $importer->resolveCityId($profileData['city'] ?? null, $citySlug);
                if (! $cityId) {
                    $this->warn("Could not resolve cities.id for '{$citySlug}' — profiles will be imported with city=NULL. Add a matching row to the cities table.");
                }
            }

            try {
                $result = $importer->import($row, $citySlug, $cityId);
                if ($result === null) {
                    continue;
                }
                $saved++;
                $this->line("  + imported {$row->external_id} → user #{$result['user_id']}, profile #{$result['profile_id']}, {$result['images']} image(s)");
            } catch (\Throwable $e) {
                $this->error("  ! import failed for {$row->external_id}: " . $e->getMessage());
            }
        }

        if ($importEnabled) {
            $this->info("Done. Imported {$saved}, skipped-no-phone {$skippedNoPhone}, skipped-already {$skippedAlreadyImported}.");
        } else {
            $this->info("Done. Scraped {$saved} into ivysociete_profiles (import skipped). skipped-no-phone {$skippedNoPhone}.");
        }

        $this->markRunCompleted($run, "imported {$saved} profile(s)");

        return Command::SUCCESS;
    }

    private function loadRun(): ?ScraperRun
    {
        $id = $this->option('run-id');
        if (! $id) return null;
        return ScraperRun::find((int) $id);
    }

    private function markRunRunning(?ScraperRun $run, string $stage): void
    {
        if (! $run) return;
        $run->update([
            'status'         => ScraperRun::STATUS_RUNNING,
            'started_at'     => now(),
            'progress_stage' => $stage,
        ]);
    }

    private function updateRunProgress(?ScraperRun $run, int $current, int $total, string $stage): void
    {
        if (! $run) return;
        $run->update([
            'progress_current' => $current,
            'progress_total'   => $total,
            'progress_stage'   => $stage,
        ]);
    }

    private function markRunCompleted(?ScraperRun $run, string $stage): void
    {
        if (! $run) return;
        $run->update([
            'status'         => ScraperRun::STATUS_COMPLETED,
            'progress_stage' => $stage,
            'completed_at'   => now(),
            'exit_code'      => 0,
        ]);
        $this->launchNextPending($run->source);
    }

    private function markRunFailed(?ScraperRun $run, string $error): void
    {
        if (! $run) return;
        $run->update([
            'status'        => ScraperRun::STATUS_FAILED,
            'error_message' => $error,
            'completed_at'  => now(),
            'exit_code'     => 1,
        ]);
        $this->launchNextPending($run->source);
    }

    private function launchNextPending(string $source): void
    {
        try {
            app(\App\Services\ScraperRunLauncher::class)->launchNextPendingIfIdle($source);
        } catch (\Throwable $e) {
            $this->warn('Failed to launch next queued run: ' . $e->getMessage());
        }
    }
}
