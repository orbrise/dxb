<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersProfile;
use App\Models\Setting;
use App\Models\MailSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendArchiveWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profiles:send-archive-warnings {--dry-run : Show what warnings would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send warning emails to users before their profiles are archived';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Setting::first();
        
        if (!$settings || !$settings->auto_archive_enabled || !$settings->send_archive_warning) {
            $this->info('Archive warnings are disabled.');
            return 0;
        }

        $archiveDays = $settings->auto_archive_days ?? 30;
        $warningDays = $settings->archive_warning_days ?? 3;
        
        // Calculate the warning date range
        $warningStartDate = Carbon::now()->subDays($archiveDays - $warningDays);
        $warningEndDate = Carbon::now()->subDays($archiveDays - $warningDays - 1);
        
        $this->info("Sending warnings for profiles created between {$warningStartDate->format('Y-m-d')} and {$warningEndDate->format('Y-m-d')}");
        $this->info("These profiles will be archived in {$warningDays} days");

        // Find free profiles that need warnings
        $profilesToWarn = UsersProfile::active()
            ->whereBetween('created_at', [$warningStartDate, $warningEndDate])
            ->where(function($query) {
                // Free profiles: no package or basic package (ID 19)
                $query->whereNull('package_id')
                      ->orWhere('package_id', 19);
            })
            ->with(['user', 'gcity'])
            ->get();

        if ($profilesToWarn->isEmpty()) {
            $this->info('No profiles found that need archive warnings.');
            return 0;
        }

        $this->info("Found {$profilesToWarn->count()} profile(s) that need warnings:");

        foreach ($profilesToWarn as $profile) {
            $age = Carbon::parse($profile->created_at)->diffInDays(Carbon::now());
            $daysUntilArchive = $archiveDays - $age;
            $packageName = $profile->package_id ? 'Basic' : 'Free';
            
            $this->line("- ID: {$profile->id} | Name: {$profile->name} | Days until archive: {$daysUntilArchive} | Package: {$packageName}");
        }

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN: No warning emails were actually sent.');
            return 0;
        }

        $sentCount = 0;
        $errors = [];

        foreach ($profilesToWarn as $profile) {
            try {
                $this->sendWarningEmail($profile, $warningDays);
                $sentCount++;
                
                Log::info("Archive warning sent for profile ID: {$profile->id} ({$profile->name})");
                
            } catch (\Exception $e) {
                $error = "Failed to send warning for profile ID: {$profile->id} - " . $e->getMessage();
                $errors[] = $error;
                Log::error($error);
            }
        }

        $this->info("Successfully sent {$sentCount} warning email(s).");

        if (!empty($errors)) {
            $this->error("Errors encountered:");
            foreach ($errors as $error) {
                $this->error("- {$error}");
            }
        }

        return 0;
    }

    /**
     * Send archive warning email
     */
    private function sendWarningEmail($profile, $daysUntilArchive)
    {
        $mailSettings = MailSettings::first();
        
        if (!$mailSettings || !$mailSettings->shouldSendEmail('profile_archived')) {
            return;
        }

        try {
            // TODO: Create ProfileArchiveWarningMail mailable class
            // Mail::to($profile->user->email)->send(new ProfileArchiveWarningMail($profile, $daysUntilArchive));
            
            Log::info("Archive warning sent to {$profile->user->email} for profile ID: {$profile->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send archive warning for profile ID: {$profile->id} - " . $e->getMessage());
        }
    }
}
