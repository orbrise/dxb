<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon; 

class ProfilesAutoDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Options:
     *  --dry-run   Only log what would be deleted without actually deleting
     */
    protected $signature = 'profiles:auto-delete {--dry-run}';

    /**
     * The console command description.
     */
    protected $description = 'Auto delete archived or inactive profiles based on settings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $settings = Setting::first();
        if (!$settings) {
            $this->warn('Settings not found. Skipping auto-delete.');
            return self::SUCCESS;
        }

        $deletedCount = 0;

        // 1) Delete archived profiles older than threshold
        if ($settings->auto_delete_archived_enabled && $settings->auto_delete_archived_days) {
            $threshold = now()->subDays((int) $settings->auto_delete_archived_days);
            $archivedQuery = UsersProfile::whereNotNull('archived_at')
                ->where('archived_at', '<=', $threshold)
                ->whereNull('package_id'); // exclude premium

            $toDelete = $archivedQuery->count();
            $this->info("Archived profiles to delete: {$toDelete} (threshold: {$threshold})");

            if ($toDelete > 0) {
                $archivedQuery->chunkById(200, function ($profiles) use (&$deletedCount, $dryRun) {
                    foreach ($profiles as $profile) {
                        $logMsg = "Deleting ARCHIVED profile #{$profile->id} (user: {$profile->user_id}, name: {$profile->name})";
                        if ($dryRun) {
                            \Log::info('[DRY RUN] ' . $logMsg);
                            $this->line('[DRY RUN] ' . $logMsg);
                        } else {
                            $this->deleteProfileWithRelations($profile);
                            $deletedCount++;
                            \Log::info($logMsg);
                        }
                    }
                });
            }
        }

        // 2) Delete inactive profiles older than threshold
        if ($settings->auto_delete_inactive_enabled && $settings->auto_delete_inactive_days) {
            $threshold = now()->subDays((int) $settings->auto_delete_inactive_days);
            $inactiveQuery = UsersProfile::where('is_active', 0)
                ->where(function($q){
                    $q->whereNotNull('archived_at')->orWhereNull('archived_at');
                })
                ->where('updated_at', '<=', $threshold)
                ->whereNull('package_id'); // exclude premium

            $toDelete = $inactiveQuery->count();
            $this->info("Inactive profiles to delete: {$toDelete} (threshold: {$threshold})");

            if ($toDelete > 0) {
                $inactiveQuery->chunkById(200, function ($profiles) use (&$deletedCount, $dryRun) {
                    foreach ($profiles as $profile) {
                        $logMsg = "Deleting INACTIVE profile #{$profile->id} (user: {$profile->user_id}, name: {$profile->name})";
                        if ($dryRun) {
                            \Log::info('[DRY RUN] ' . $logMsg);
                            $this->line('[DRY RUN] ' . $logMsg);
                        } else {
                            $this->deleteProfileWithRelations($profile);
                            $deletedCount++;
                            \Log::info($logMsg);
                        }
                    }
                });
            }
        }

        $this->info("Total profiles deleted: {$deletedCount}" . ($dryRun ? ' (dry run)' : ''));
        return self::SUCCESS;
    }

    protected function deleteProfileWithRelations(UsersProfile $profile): void
    {
        // Remove related data if cascading isn't configured
        try {
            // Example relations cleanup; adjust if DB has ON DELETE CASCADE
            $profile->services()->delete();
            $profile->languages()->delete();
            $profile->reviews()->delete();
            $profile->questions()->delete();
            $profile->multipleimgs()->delete(); // may select subset; ensure full delete by direct relation if needed
            $profile->singleimg()->delete();
            $profile->coverimg()->delete();
        } catch (\Throwable $e) {
            \Log::warning('Error cleaning profile relations for ID ' . $profile->id . ': ' . $e->getMessage());
        }

        $profile->delete();
    }
}
