<?php

namespace App\Observers;

use App\Models\UsersProfile;
use App\Services\CacheService;

class ProfileObserver
{
    /**
     * Handle the UsersProfile "created" event.
     */
    public function created(UsersProfile $profile): void
    {
        // Clear homepage caches when a new profile is created
        CacheService::clearHomepageCache($profile->city, $profile->gender);
    }

    /**
     * Handle the UsersProfile "updated" event.
     */
    public function updated(UsersProfile $profile): void
    {
        // Clear this profile's cache
        CacheService::clearProfileCache($profile->id);
        
        // Clear homepage caches if relevant fields changed
        $relevantFields = ['is_active', 'package_id', 'city', 'gender', 'archived_at'];
        if ($profile->wasChanged($relevantFields)) {
            CacheService::clearHomepageCache($profile->city, $profile->gender);
        }
    }

    /**
     * Handle the UsersProfile "deleted" event.
     */
    public function deleted(UsersProfile $profile): void
    {
        CacheService::clearProfileCache($profile->id);
        CacheService::clearHomepageCache($profile->city, $profile->gender);
    }
}
