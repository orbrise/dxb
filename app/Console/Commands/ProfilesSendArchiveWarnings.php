<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Log;

class ProfilesSendArchiveWarnings extends Command
{
    protected $signature = 'profiles:send-archive-warnings';
    protected $description = 'Send warning emails before profiles are archived';

    public function handle(): int
    {
        $settings = Setting::first();
        if (!$settings || !$settings->send_archive_warning) {
            $this->line('Archive warning emails disabled or settings missing.');
            return self::SUCCESS;
        }

        $days = (int) ($settings->auto_archive_days ?? 30);
        $warnBefore = (int) ($settings->archive_warning_days ?? 3);
        $threshold = now()->subDays($days - $warnBefore);

        $query = UsersProfile::whereNull('package_id')
            ->whereNull('archived_at')
            ->where('created_at', '<=', $threshold);

        $query->chunkById(200, function ($profiles) {
            foreach ($profiles as $profile) {
                // TODO: implement actual mail sending.
                \Log::info("Would send archive warning for profile #{$profile->id} (user {$profile->user_id})");
            }
        });

        return self::SUCCESS;
    }
}
