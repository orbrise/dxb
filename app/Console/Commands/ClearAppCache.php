<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class ClearAppCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-cache 
                            {--profile= : Clear cache for a specific profile ID}
                            {--lookups : Clear only lookup table caches}
                            {--all : Clear all application caches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear application caches for performance optimization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($profileId = $this->option('profile')) {
            CacheService::clearProfileCache($profileId);
            $this->info("Cleared cache for profile ID: {$profileId}");
            return 0;
        }

        if ($this->option('lookups')) {
            CacheService::clearLookupCaches();
            $this->info('Cleared all lookup table caches (services, currencies, etc.)');
            return 0;
        }

        if ($this->option('all')) {
            CacheService::clearAllCaches();
            $this->info('Cleared all application caches');
            return 0;
        }

        // Default: clear homepage caches
        CacheService::clearHomepageCache();
        $this->info('Cleared homepage caches');
        
        return 0;
    }
}
