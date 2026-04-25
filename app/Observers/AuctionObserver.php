<?php

namespace App\Observers;

use App\Models\Auction;
use App\Services\CacheService;
use App\Services\CacheVersion;
use App\Services\CloudflarePurge;

class AuctionObserver
{
    public function created(Auction $auction): void
    {
        $this->invalidate($auction);
    }

    public function updated(Auction $auction): void
    {
        $this->invalidate($auction);

        // If city or gender moved, also invalidate the old scope.
        if ($auction->wasChanged(['city_id', 'gender'])) {
            $original = $auction->getOriginal();
            CacheVersion::bump(CacheVersion::auctionScope(
                $original['city_id'] ?? null,
                $original['gender'] ?? null
            ));
        }

        // Listing excludes current auction winner profiles — bump the listing cache too.
        if ($auction->wasChanged(['winner_profile_id', 'status'])) {
            CacheVersion::bump(CacheVersion::listingScope(
                $auction->city_id,
                $this->genderIdFor($auction->gender)
            ));
        }
    }

    public function deleted(Auction $auction): void
    {
        $this->invalidate($auction);
        CacheVersion::bump(CacheVersion::listingScope(
            $auction->city_id,
            $this->genderIdFor($auction->gender)
        ));
    }

    protected function invalidate(Auction $auction): void
    {
        CacheVersion::bump(CacheVersion::auctionScope($auction->city_id, $auction->gender));

        // Edge-purge: the listing page shows auction banners, so sweep that tree.
        $city = CacheService::getCityById($auction->city_id);
        if ($city && $auction->gender) {
            CloudflarePurge::purgeListing(
                strtolower($auction->gender),
                $city->slug ?: strtolower($city->name)
            );
        }
    }

    /**
     * Auction.gender is stored as the gender name ('female'), but listing scope
     * keys by gender_id. Resolve via cached lookup.
     */
    protected function genderIdFor($genderName): ?int
    {
        if (!$genderName) {
            return null;
        }
        $model = \App\Services\CacheService::getGenderByName($genderName);
        return $model ? (int) $model->id : null;
    }
}
