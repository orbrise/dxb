<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\CacheService;
use App\Services\CacheVersion;
use App\Services\CloudflarePurge;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $this->invalidate($review);
    }

    public function updated(Review $review): void
    {
        $this->invalidate($review);
    }

    public function deleted(Review $review): void
    {
        $this->invalidate($review);
    }

    protected function invalidate(Review $review): void
    {
        // A review changes the profile-detail page (reviews list, review count badge).
        CacheVersion::bump(CacheVersion::profileScope($review->profile_id));

        // The listing shows $profile->reviews->count() — bump the listing scope too.
        $profile = $review->relationLoaded('profile') ? $review->profile : $review->profile()->first();
        if ($profile) {
            CacheVersion::bump(CacheVersion::listingScope($profile->city, $profile->gender));
        }

        // Invalidate page cache since review counts appear on listing pages
        CacheVersion::bump(CacheVersion::pageScope());

        Cache::forget('cache:recent_reviews:10');
        Cache::forget('cache:recent_reviews:30');

        // Edge-purge Cloudflare so the review shows immediately.
        if ($profile) {
            $city = CacheService::getCityById($profile->city);
            $gender = CacheService::getGenderById($profile->gender);
            if ($city && $gender) {
                CloudflarePurge::purgeListing(
                    strtolower($gender->name),
                    $city->slug ?: strtolower($city->name)
                );
            }
        }
    }
}
