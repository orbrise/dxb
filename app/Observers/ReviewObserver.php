<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->clearCaches($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $this->clearCaches($review);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->clearCaches($review);
    }

    /**
     * Clear relevant caches
     */
    protected function clearCaches(Review $review): void
    {
        // Clear profile-specific review cache
        Cache::forget("cache:profile:reviews:{$review->profile_id}");
        
        // Clear recent reviews cache
        Cache::forget('cache:recent_reviews:10');
        Cache::forget('cache:recent_reviews:30');
    }
}
