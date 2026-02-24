<?php

namespace App\Observers;

use App\Models\ProfileImage;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class ProfileImageObserver
{
    /**
     * Handle the ProfileImage "created" event.
     */
    public function created(ProfileImage $image): void
    {
        $this->clearCaches($image);
    }

    /**
     * Handle the ProfileImage "updated" event.
     */
    public function updated(ProfileImage $image): void
    {
        $this->clearCaches($image);
    }

    /**
     * Handle the ProfileImage "deleted" event.
     */
    public function deleted(ProfileImage $image): void
    {
        $this->clearCaches($image);
    }

    /**
     * Clear relevant caches
     */
    protected function clearCaches(ProfileImage $image): void
    {
        // Clear profile images cache
        Cache::forget("cache:profile:images:{$image->profile_id}");
        
        // Clear profile detail cache (as it includes images)
        Cache::forget("cache:profile:detail:{$image->profile_id}");
    }
}
