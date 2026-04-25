<?php

namespace App\Observers;

use App\Models\AuctionBid;
use App\Services\CacheVersion;

class AuctionBidObserver
{
    public function created(AuctionBid $bid): void
    {
        $this->invalidate($bid);
    }

    public function updated(AuctionBid $bid): void
    {
        $this->invalidate($bid);
    }

    public function deleted(AuctionBid $bid): void
    {
        $this->invalidate($bid);
    }

    /**
     * A bid changes the auction's current_price display — invalidate the
     * auction scope for that auction's city+gender.
     */
    protected function invalidate(AuctionBid $bid): void
    {
        $auction = $bid->relationLoaded('auction') ? $bid->auction : $bid->auction()->first();
        if ($auction) {
            CacheVersion::bump(CacheVersion::auctionScope($auction->city_id, $auction->gender));
        }
    }
}
