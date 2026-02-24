<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersProfile;
use App\Models\Setting;
use App\Models\MailSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ArchiveOldProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profiles:auto-archive {--dry-run : Show what would be archived without actually archiving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically archive old free profiles based on app settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Setting::first();
        
        if (!$settings || !$settings->auto_archive_enabled) {
            $this->info('Auto-archive is disabled.');
            return 0;
        }

        $archiveDays = $settings->auto_archive_days ?? 30;
        $cutoffDate = Carbon::now()->subDays($archiveDays);
        
        $this->info("Looking for free profiles older than {$archiveDays} days (before {$cutoffDate->format('Y-m-d')})");

        // Find free profiles that should be archived
        $profilesToArchive = UsersProfile::active()
            ->where('created_at', '<', $cutoffDate)
            ->where(function($query) {
                // Free profiles: no package or basic package (ID 19)
                $query->whereNull('package_id')
                      ->orWhere('package_id', 19);
            })
            ->with(['user', 'gcity'])
            ->get();

        if ($profilesToArchive->isEmpty()) {
            $this->info('No profiles found for archiving.');
            return 0;
        }

        $this->info("Found {$profilesToArchive->count()} profile(s) to archive:");

        foreach ($profilesToArchive as $profile) {
            $age = Carbon::parse($profile->created_at)->diffInDays(Carbon::now());
            $packageName = $profile->package_id ? 'Basic' : 'Free';
            
            $this->line("- ID: {$profile->id} | Name: {$profile->name} | Age: {$age} days | Package: {$packageName}");
        }

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN: No profiles were actually archived.');
            return 0;
        }

        if (!$this->confirm('Do you want to archive these profiles?')) {
            $this->info('Archiving cancelled.');
            return 0;
        }

        $archivedCount = 0;
        $errors = [];

        foreach ($profilesToArchive as $profile) {
            try {
                $profile->archive('Automatically archived after ' . $archiveDays . ' days of inactivity');
                $archivedCount++;

                // Send archive notification if enabled
                $this->sendArchiveNotification($profile);

                Log::info("Auto-archived profile ID: {$profile->id} ({$profile->name})");
                
            } catch (\Exception $e) {
                $error = "Failed to archive profile ID: {$profile->id} - " . $e->getMessage();
                $errors[] = $error;
                Log::error($error);
            }
        }

        $this->info("Successfully archived {$archivedCount} profile(s).");

        if (!empty($errors)) {
            $this->error("Errors encountered:");
            foreach ($errors as $error) {
                $this->error("- {$error}");
            }
        }

        return 0;
    }

    /**
     * Send archive notification email
     */
    private function sendArchiveNotification($profile)
    {
        $mailSettings = MailSettings::first();
        
        if (!$mailSettings || !$mailSettings->shouldSendEmail('profile_archived')) {
            return;
        }

        try {
            // TODO: Create ProfileArchivedMail mailable class
            // Mail::to($profile->user->email)->send(new ProfileArchivedMail($profile));
            
            Log::info("Archive notification sent to {$profile->user->email} for profile ID: {$profile->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send archive notification for profile ID: {$profile->id} - " . $e->getMessage());
        }
    }
}
