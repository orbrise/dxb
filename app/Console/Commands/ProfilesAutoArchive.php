<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\UsersProfile;

class ProfilesAutoArchive extends Command
{
    protected $signature = 'profiles:auto-archive';
    protected $description = 'Auto-archive free profiles after configured days';

    public function handle(): int
    {
        $settings = Setting::first();
        if (!$settings || !$settings->auto_archive_enabled) {
            $this->line('Auto-archive disabled or settings missing.');
            return self::SUCCESS;
        }

        $days = (int) ($settings->auto_archive_days ?? 30);
        $threshold = now()->subDays($days);

        $query = UsersProfile::whereNull('package_id') // free profiles only
            ->whereNull('archived_at')
            ->where('created_at', '<=', $threshold);

        $count = $query->count();
        $this->info("Profiles to archive: {$count} (threshold: {$threshold})");

        $query->chunkById(200, function ($profiles) {
            foreach ($profiles as $profile) {
                $profile->archive('Auto-archived after inactivity period');
            }
        });

        return self::SUCCESS;
    }
}
