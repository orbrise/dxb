<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'spot_number',
        'starting_price',
        'current_price',
        'end_date',
        'status', // 'active', 'ended'
        'winner_id',
        'winner_profile_id',
        'city_id',
        'gender',
        'listing_id',
        'background_image',
    ];

    protected $casts = [
        'end_date' => 'datetime',
    ];

    /** Credits a user must have put through their wallet before they can bid. */
    public const MIN_SPENT_CREDITS = 300;

    public static function userQualifies(?User $user): bool
    {
        $wallet = $user?->wallet;
        if (!$wallet) return false;

        return $wallet->transactions()->where('status', 'completed')->sum('amount') >= self::MIN_SPENT_CREDITS;
    }

    /** Taking bids: active and the end date not reached (auctions:end closes it hourly). */
    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    /** The price each new round opens at. */
    public function getRoundStartPriceAttribute()
    {
        return $this->starting_price ?? $this->current_price;
    }

    /** Escort auctions are keyed by gender (listing_id NULL). */
    public const GENDERS = ['female' => 'Escorts', 'male' => 'Male Escorts', 'shemale' => 'Shemale Escorts'];

    protected static ?array $categories = null;

    /**
     * Auction categories in tab order: the escort genders, then every other
     * listing category (Admin → Categories), which is keyed by listing_id.
     * Listing rows that repeat an escort gender ("Male Escorts") are left to
     * that gender's tab.
     *
     * key => [label, gender, listing_id, prefix]; prefix is the URL segment
     * before "-in-{city}", e.g. "female-escorts" or "massage".
     */
    public static function categories(): array
    {
        if (static::$categories !== null) return static::$categories;

        $categories = [];
        foreach (self::GENDERS as $gender => $label) {
            $categories[$gender] = ['label' => $label, 'gender' => $gender, 'listing_id' => null, 'prefix' => $gender . '-escorts'];
        }

        $genderLabels = array_map('strtolower', self::GENDERS);
        Listing::query()
            ->where('is_escort_category', false)
            ->orderBy('sort_order')->orderBy('id')
            ->get(['id', 'name', 'slug'])
            ->each(function (Listing $listing) use (&$categories, $genderLabels) {
                if (in_array(strtolower(trim($listing->name)), $genderLabels, true)) return;
                $key = $listing->slug ?: Str::slug($listing->name);
                if ($key === '' || isset($categories[$key])) return;
                $categories[$key] = ['label' => $listing->name, 'gender' => null, 'listing_id' => (int) $listing->id, 'prefix' => $key];
            });

        return static::$categories = $categories;
    }

    /**
     * The category key for a URL: /auctions/{gender}-escorts-in-… binds
     * $gender, /auctions/{category}-in-… binds $category. A category whose
     * slug ends in "-escorts" lands in the gender route, so try that too.
     */
    public static function resolveCategory(?string $gender, ?string $category = null): ?string
    {
        $categories = self::categories();
        foreach (array_filter([$category, $gender, $gender ? $gender . '-escorts' : null]) as $key) {
            if (isset($categories[$key])) return $key;
        }
        return null;
    }

    public function scopeInCategory($query, string $key)
    {
        $category = self::categories()[$key] ?? null;
        if (!$category) return $query->whereRaw('1 = 0');

        return $category['listing_id']
            ? $query->where('listing_id', $category['listing_id'])
            : $query->whereNull('listing_id')->where('gender', $category['gender']);
    }

    public function getCategoryKeyAttribute(): string
    {
        if (!$this->listing_id) return (string) $this->gender;

        foreach (self::categories() as $key => $category) {
            if ($category['listing_id'] === (int) $this->listing_id) return $key;
        }
        return 'listing-' . $this->listing_id;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categories()[$this->category_key]['label']
            ?? Listing::whereKey($this->listing_id)->value('name')
            ?? ucfirst((string) $this->gender);
    }

    /** URL segment before "-in-{city}" for this auction's category. */
    public function getUrlPrefixAttribute(): string
    {
        return self::categories()[$this->category_key]['prefix'] ?? ($this->gender . '-escorts');
    }

    /** Cache scope part (CacheVersion::auctionScope): the gender, or the listing. */
    public static function scopeKey(?string $gender, $listingId = null): ?string
    {
        return $listingId ? 'listing-' . $listingId : $gender;
    }

    /** Shown for a slot when an admin hasn't uploaded an image (public/). */
    public const DEFAULT_IMAGE = 'assets/images/auction-spot-default.svg';

    public static function defaultImageUrl(): string
    {
        return asset(self::DEFAULT_IMAGE);
    }

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
    /** The slot's image: the one an admin uploaded, else the default. */
    public function getBackgroundImageUrlAttribute()
    {
        if (! $this->background_image) {
            return self::defaultImageUrl();
        }
        if (filter_var($this->background_image, FILTER_VALIDATE_URL)) {
            return $this->background_image;
        }
        return rtrim(config('filesystems.disks.assets_external.url', 'https://assets.evoory.com'), '/')
            . '/auctions/' . basename($this->background_image);
    }

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
