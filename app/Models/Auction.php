<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'spot_number',
        'current_price',
        'end_date',
        'status', // 'active', 'ended'
        'winner_id',
        'winner_profile_id',
        'city_id', 
        'gender'
    ];
 
    protected $casts = [
        'end_date' => 'datetime',
    ];

    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function winnerProfile()
    {
        return $this->belongsTo(UsersProfile::class, 'winner_profile_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    
    public function getTimeLeftAttribute()
    {
        if ($this->end_date->isPast()) {
            return 'Ended';
        }
        
        return $this->end_date->diffForHumans(['parts' => 1]);
    }
    
    public function getDaysLeftAttribute()
    {
        if ($this->end_date->isPast()) {
            return 0;
        }
        
        return $this->end_date->diffInDays(Carbon::now());
    }
    
    public function getIsExpiredAttribute()
    {
        return $this->end_date->isPast();
    }
    
    public function getHighestBidAttribute()
    {
        return $this->bids()->orderBy('amount', 'desc')->first();
    }
    
    public function getBidCountAttribute()
    {
        return $this->bids()->count();
    }
    
    /**
     * Check if the spot is still valid for display
     * For ended auctions with winners, spot is valid until end_date + duration_days
     */
    public function getIsSpotValidAttribute()
    {
        if ($this->status === 'active') {
            return true;
        }
        
        // For ended auctions with winners, check if spot is still within validity period
        if ($this->status === 'ended' && $this->winner_profile_id) {
            $durationDays = $this->duration_days ?? 7;
            $spotExpiryDate = Carbon::parse($this->end_date)->addDays($durationDays);
            return Carbon::now()->lt($spotExpiryDate);
        }
        
        return false;
    }
    
    /**
     * Get the expiry date of the spot for winners
     */
    public function getSpotExpiryDateAttribute()
    {
        $durationDays = $this->duration_days ?? 7;
        return Carbon::parse($this->end_date)->addDays($durationDays);
    }
    
    /**
     * Scope to get only auctions with valid spots (active OR ended but spot not yet expired)
     * end_date represents when the spot expires for the winner
     */
    public function scopeWithValidSpot($query)
    {
        $now = Carbon::now();
        
        return $query->where(function($q) use ($now) {
            // Active auctions
            $q->where('status', 'active')
              // Or ended auctions where end_date (spot expiry) > now
              ->orWhere(function($q2) use ($now) {
                  $q2->where('status', 'ended')
                     ->whereNotNull('winner_profile_id')
                     ->where('end_date', '>', $now);
              });
        });
    }
}
