<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UsersProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredPackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packages:check-expired {--dry-run : Preview which profiles would be reset to free without making changes} {--fix-missing : Also fix profiles with missing expiry dates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired packages and reset profiles to free';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $fixMissing = $this->option('fix-missing');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE: No changes will be made');
        }
        
        $this->info('Checking for expired packages...');
        
        $expiredProfiles = collect();
        
        // 1. Find profiles with package_expires_at in the past
        $expiredByDate = UsersProfile::whereNotNull('package_id')
            ->whereNotNull('package_expires_at')
            ->where('package_expires_at', '<', Carbon::now())
            ->with(['package:id,name', 'gcity:id,name'])
            ->get();
        
        $expiredProfiles = $expiredProfiles->merge($expiredByDate);
        
        // 2. Find profiles with package but no expiry date (needs fixing)
        if ($fixMissing) {
            $missingExpiry = UsersProfile::whereNotNull('package_id')
                ->whereNull('package_expires_at')
                ->whereNotNull('package_days')
                ->with(['package:id,name', 'gcity:id,name'])
                ->get()
                ->filter(function ($profile) {
                    // Check if created_at + package_days is in the past
                    $shouldExpireAt = Carbon::parse($profile->created_at)->addDays($profile->package_days);
                    return $shouldExpireAt->isPast();
                });
            
            $expiredProfiles = $expiredProfiles->merge($missingExpiry);
        }
        
        if ($expiredProfiles->isEmpty()) {
            $this->info('No expired packages found.');
            return 0;
        }
        
        $this->info("Found {$expiredProfiles->count()} profile(s) with expired packages:");
        $this->newLine();
        
        // Display table of expired profiles
        $tableData = [];
        foreach ($expiredProfiles as $profile) {
            if ($profile->package_expires_at) {
                $expiryDate = Carbon::parse($profile->package_expires_at)->format('Y-m-d H:i');
                $daysExpired = Carbon::parse($profile->package_expires_at)->diffInDays(Carbon::now());
            } else {
                // Calculate from created_at + package_days
                $shouldExpireAt = Carbon::parse($profile->created_at)->addDays($profile->package_days ?? 0);
                $expiryDate = $shouldExpireAt->format('Y-m-d H:i') . ' (calculated)';
                $daysExpired = $shouldExpireAt->diffInDays(Carbon::now());
            }
            
            $tableData[] = [
                'ID' => $profile->id,
                'Name' => $profile->name,
                'City' => $profile->gcity->name ?? 'N/A',
                'Package' => $profile->package->name ?? 'N/A',
                'Package Days' => $profile->package_days ?? 'N/A',
                'Expired On' => $expiryDate,
                'Days Expired' => $daysExpired,
            ];
        }
        
        $this->table(
            ['ID', 'Name', 'City', 'Package', 'Package Days', 'Expired On', 'Days Expired'],
            $tableData
        );
        
        if ($isDryRun) {
            $this->warn('DRY RUN: The above profiles would be reset to free package.');
            return 0;
        }
        
        // Reset the profiles to free
        $resetCount = 0;
        $errors = [];
        
        foreach ($expiredProfiles as $profile) {
            try {
                $oldPackageId = $profile->package_id;
                $oldPackageName = $profile->package->name ?? 'Unknown';
                
                // Reset to free package
                $profile->update([
                    'package_id' => null,
                    'package_days' => null,
                    'package_expires_at' => null,
                    'is_featured' => 0, // Also remove featured status
                ]);
                
                $resetCount++;
                
                // Log the action
                Log::info("Package expired and profile reset to free", [
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->name,
                    'user_id' => $profile->user_id,
                    'old_package_id' => $oldPackageId,
                    'old_package_name' => $oldPackageName,
                    'expired_on' => $profile->package_expires_at ?? 'calculated from creation date',
                ]);
                
                $this->line("✓ Reset profile to free: {$profile->name} (ID: {$profile->id}) - was {$oldPackageName}");
                
            } catch (\Exception $e) {
                $errors[] = [
                    'profile_id' => $profile->id,
                    'error' => $e->getMessage()
                ];
                
                $this->error("✗ Failed to reset profile ID {$profile->id}: {$e->getMessage()}");
                
                Log::error("Failed to reset expired package profile", [
                    'profile_id' => $profile->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->newLine();
        $this->info("Summary: Reset {$resetCount} profile(s) to free package");
        
        if (!empty($errors)) {
            $this->error("Errors: " . count($errors) . " profile(s) failed to update");
        }
        
        return 0;
    }
}
