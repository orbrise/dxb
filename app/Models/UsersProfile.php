<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UsersProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'listing',
        'city',
        'about',
        'country_code',
        'phone',
        'iswhatsapp',
        'istelegram', 
        'iswechat',
        'issignal',
        'country_code2',
        'phone2',
        'iswhatsapp2',
        'istelegram2',
        'iswechat2',
        'issignal2',
        'website',
        'onlyfans',
        'gender',
        'incall',
        'outcall',
        'orientation',
        'height',
        'haircolor',
        'nationality',
        'bust',
        'age',
        'incallprice',
        'outcallprice',
        'ethnicity',
        'incallcurr',
        'outcallcurr',
        'shaved',
        'smoke',
        'video',
        'ip_address',
        'ip_country',
        'tweets',
        'is_active',
        'is_featured',
        'package_id',
        'slug',
        'promoted_until',
        'package_expires_at',
        'is_online',
        'archived_at',
        'archive_reason',
        'photo_code',
        'is_verified',
        'profile_views',
        'phone_clicks',
        'package_days',
        'created_at'
    ];
 
    protected $casts = [
        'archived_at' => 'datetime',
        'promoted_until' => 'datetime',
    ];

    public function getcity(){
        return $this->hasOne(City::class, "id", "city");
    }

    public function getcountry(){
        return $this->hasOne(Country::class, "id", "nationality");
    }
 
    public function getgender(){
        return $this->hasOne(Gender::class, "id", "gender");
    }

    public function coverimg(){
        return $this->hasOne(ProfileImage::class, 'profile_id', 'id')->where('is_main', 1);
    }

    public function singleimg(){
        return $this->hasOne(ProfileImage::class, 'profile_id', 'id');
    }

    public function multipleimgs() {
        return $this->hasMany(ProfileImage::class, 'profile_id', 'id')
            ->where(function($query) {
                $query->whereNull('is_main')
                      ->orWhere('is_main', '!=', 1);
            })->take(3);
    }
    
     public function multipleimgss() {
        return $this->hasMany(ProfileImage::class, 'profile_id', 'id')
            ->where(function($query) {
                $query->whereNull('is_main')
                      ->orWhere('is_main', '!=', 1);
            })
            ->take(3);
    }
    
    /**
     * Get all profile images (for admin panel - no limit)
     */
    public function allImages() {
        return $this->hasMany(ProfileImage::class, 'profile_id', 'id')
            ->orderBy('image_order', 'asc');
    }

    public function languages(){
        return $this->hasMany(UserLanguage::class, 'profile_id', 'id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function ori(){
        return $this->hasOne(Orientation::class, 'id', 'orientation');
    }


    public function ethi(){
        return $this->hasOne(Ethnicity::class, 'id', 'ethnicity');
    }

    public function gbust(){
        return $this->hasOne(Bust::class, 'id', 'bust');
    }

    public function ghair(){
        return $this->hasOne(HairColor::class, 'id', 'haircolor');
    }

    public function gnat(){
        return $this->hasOne(Country::class, 'id', 'nationality');
    }

    public function ggender(){
        return $this->hasOne(Gender::class, 'id', 'gender');
    }

    public function gcity(){
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function user()
{
    return $this->belongsTo(User::class);
}

public function glisting()
    {
        return $this->hasOne(Listing::class, 'id', 'listing');
    }

    public function services()
    {
        return $this->hasMany(UserService::class, 'profile_id', 'id');
    }

    public function photoverify() {
        return $this->hasOne(VerificationPhoto::class, 'profile_id', 'id')->where('status', 'approved');
    }

    public function rejectedVerification() {
        return $this->hasOne(VerificationPhoto::class, 'profile_id', 'id')
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc');
    }

public function getpackage(){
return $this->hasOne(Package::class, 'id', 'package_id');
}
    
public function reviews()
{
    return $this->hasMany(Review::class, 'profile_id');
}

public function questions()
{
    return $this->hasMany(Question::class, 'profile_id');
}
 
/**
 * Get the active auction where this profile is the winner
 * A profile is considered "auctioned" if:
 * - They are set as winner_profile_id
 * - The auction end_date is still in the future
 */
public function activeAuction()
{
    return $this->hasOne(Auction::class, 'winner_profile_id')
        ->where('end_date', '>', now());
}

/**
 * Get all auctions won by this profile
 */
public function auctionsWon()
{
    return $this->hasMany(Auction::class, 'winner_profile_id');
}

/**
 * Check if profile is currently an auction winner
 */
public function isAuctioned()
{
    return $this->activeAuction()->exists();
}

/**
 * Get auction days remaining
 */
public function getAuctionDaysRemainingAttribute()
{
    $auction = $this->activeAuction;
    if (!$auction) {
        return 0;
    }
    return max(0, now()->diffInDays($auction->end_date, false));
}

    // Archive-related methods
    
    /**
     * Scope to get only non-archived profiles
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope to get only archived profiles
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Check if profile is archived
     */
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    /**
     * Archive the profile
     */
    public function archive($reason = null)
    {
        $this->update([
            'archived_at' => now(),
            'archive_reason' => $reason,
            'is_active' => 0 // Also set profile as inactive
        ]);

        return $this;
    }

    /**
     * Unarchive/Repost the profile
     */
    public function repost()
    {
        $this->update([
            'archived_at' => null,
            'archive_reason' => null,
            'is_active' => 1 // Reactivate the profile
        ]);

        return $this;
    }

    /**
     * Get archive status with formatted date
     */
    public function getArchiveStatusAttribute()
    {
        if ($this->isArchived()) {
            return 'Archived on ' . $this->archived_at->format('M d, Y');
        }
        
        return 'Active';
    }

}
