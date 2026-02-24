<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredPromos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promos:check-expired {--dry-run : Preview which profiles would be unfeatured without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired promo packages and unfeatured profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE: No changes will be made');
        }
        
        $this->info('Checking for expired promo packages...');
        
        // Find profiles where:
        // 1. is_featured = 1 (currently featured)
        // 2. promoted_until is not null
        // 3. promoted_until date has passed
        $expiredProfiles = UsersProfile::where('is_featured', 1)
            ->whereNotNull('promoted_until')
            ->where('promoted_until', '<', Carbon::now())
            ->with(['user:id,name,email', 'gcity:id,name'])
            ->get();
        
        if ($expiredProfiles->isEmpty()) {
            $this->info('No expired promo packages found.');
            return 0;
        }
        
        $this->info("Found {$expiredProfiles->count()} profile(s) with expired promo packages:");
        $this->newLine();
        
        // Display table of expired profiles
        $tableData = [];
        foreach ($expiredProfiles as $profile) {
            $daysExpired = Carbon::parse($profile->promoted_until)->diffInDays(Carbon::now());
            $tableData[] = [
                'ID' => $profile->id,
                'Name' => $profile->name,
                'City' => $profile->gcity->name ?? 'N/A',
                'Package ID' => $profile->package_id ?? 'N/A',
                'Expired On' => Carbon::parse($profile->promoted_until)->format('Y-m-d H:i'),
                'Days Expired' => $daysExpired,
            ];
        }
        
        $this->table(
            ['ID', 'Name', 'City', 'Package ID', 'Expired On', 'Days Expired'],
            $tableData
        );
        
        if ($isDryRun) {
            $this->warn('DRY RUN: The above profiles would be unfeatured.');
            return 0;
        }
        
        // Unfeatured the profiles
        $unfeaturedCount = 0;
        $errors = [];
        
        foreach ($expiredProfiles as $profile) {
            try {
                // Update profile to unfeatured
                $profile->update([
                    'is_featured' => 0,
                ]);
                
                $unfeaturedCount++;
                
                // Log the action
                Log::info("Promo package expired and profile unfeatured", [
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->name,
                    'user_id' => $profile->user_id,
                    'package_id' => $profile->package_id,
                    'expired_on' => $profile->promoted_until->format('Y-m-d H:i:s'),
                ]);
                
                $this->line("✓ Unfeatured profile: {$profile->name} (ID: {$profile->id})");
                
            } catch (\Exception $e) {
                $errors[] = [
                    'profile_id' => $profile->id,
                    'error' => $e->getMessage()
                ];
                
                $this->error("✗ Failed to unfeatured profile ID {$profile->id}: {$e->getMessage()}");
                
                Log::error("Failed to unfeatured expired profile", [
                    'profile_id' => $profile->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->newLine();
        $this->info("Successfully unfeatured {$unfeaturedCount} profile(s).");
        
        if (!empty($errors)) {
            $this->error("Failed to unfeatured " . count($errors) . " profile(s).");
        }
        
        return 0;
    }
}
