<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\{
    UsersProfile, City, Service, Currency, Ethnicity,
    Bust, HairColor, Language, Country, Review, Gender, Package
};
use App\Services\CacheVersion;

class CacheService
{
    // Cache TTL constants (in seconds)
    const TTL_STATIC = 86400;      // 24 hours - for rarely changing data
    const TTL_LOOKUP = 3600;       // 1 hour - for lookup tables
    const TTL_PROFILES = 900;      // 15 minutes - for profile listings
    const TTL_PROFILE_DETAIL = 600; // 10 minutes - for single profile
    const TTL_REVIEWS = 300;       // 5 minutes - for reviews

    /**
     * Get static lookup data (services, currencies, etc.)
     */
    public static function getServices()
    {
        return Cache::remember('cache:services', self::TTL_LOOKUP, function() {
            return Service::select('id', 'name')->orderBy('name')->get();
        });
    }

    public static function getCurrencies()
    {
        return Cache::remember('cache:currencies', self::TTL_LOOKUP, function() {
            return Currency::select('id', 'code', 'country')
                ->distinct('code')
                ->groupBy('code', 'id', 'country')
                ->orderBy('code')
                ->get()
                ->unique('code')
                ->values();
        });
    }

    public static function getEthnicities()
    {
        return Cache::remember('cache:ethnicities', self::TTL_LOOKUP, function() {
            return Ethnicity::select('id', 'name')->orderBy('name')->get();
        });
    }

    public static function getBusts()
    {
        return Cache::remember('cache:busts', self::TTL_LOOKUP, function() {
            return Bust::select('id', 'name')->get();
        });
    }

    public static function getHairColors()
    {
        return Cache::remember('cache:haircolors', self::TTL_LOOKUP, function() {
            return HairColor::select('id', 'name')->orderBy('name')->get();
        });
    }

    public static function getCountries()
    {
        return Cache::remember('cache:countries', self::TTL_LOOKUP, function() {
            return Country::select('id', 'nicename', 'iso', 'phonecode')
                ->orderBy('nicename')
                ->get();
        });
    }

    public static function getLanguages()
    {
        return Cache::remember('cache:languages', self::TTL_LOOKUP, function() {
            return Language::select('id', 'name')->orderBy('name')->get();
        });
    }

    public static function getPackages()
    {
        return Cache::remember('cache:packages', self::TTL_STATIC, function() {
            return Package::select('id', 'name')->get();
        });
    }

    public static function getPackageIdsByType()
    {
        return Cache::remember('cache:package_ids_by_type', self::TTL_STATIC, function() {
            $packages = Package::select('id', 'name')->get();
            
            return [
                'vip' => $packages->filter(fn($p) => 
                    stripos($p->name, 'vip') !== false || 
                    stripos($p->name, 'premium') !== false
                )->pluck('id')->toArray(),
                
                'featured' => $packages->filter(fn($p) => 
                    stripos($p->name, 'featured') !== false
                )->pluck('id')->toArray(),
                
                'basic' => $packages->filter(fn($p) => 
                    stripos($p->name, 'basic') !== false ||
                    stripos($p->name, 'free') !== false
                )->pluck('id')->toArray(),
            ];
        });
    }

    public static function getGenderByName($name)
    {
        // genders is a tiny table; cached for TTL_STATIC. Plain equality works because
        // the column collation is *_ci (case-insensitive).
        $key = 'cache:gender:' . strtolower((string) $name);
        return Cache::remember($key, self::TTL_STATIC, function() use ($name) {
            return Gender::where('name', $name)->first();
        });
    }

    /** Cached gender-by-id lookup — used when building URLs from profile.gender (int FK). */
    public static function getGenderById($id)
    {
        if (!$id) {
            return null;
        }
        return Cache::remember("cache:gender:id:{$id}", self::TTL_STATIC, fn() => Gender::find($id));
    }

    /** Cached city-by-id lookup — used when building URLs from profile.city (int FK). */
    public static function getCityById($id)
    {
        if (!$id) {
            return null;
        }
        return Cache::remember("cache:city:id:{$id}", self::TTL_LOOKUP, fn() => City::find($id));
    }

    /**
     * Get featured cities for a country
     */
    public static function getFeaturedCities($country, $excludeCityId = null)
    {
        $cacheKey = "cache:featured_cities:{$country}";
        
        $cities = Cache::remember($cacheKey, self::TTL_LOOKUP, function() use ($country) {
            return City::where('country', $country)
                ->where('is_featured', 1)
                ->orderBy('name', 'asc')
                ->limit(15)
                ->get();
        });

        if ($excludeCityId) {
            return $cities->where('id', '!=', $excludeCityId)->take(10);
        }

        return $cities;
    }

    /**
     * Get city by slug
     */
    public static function getCityBySlug($slug)
    {
        return Cache::remember("cache:city:slug:{$slug}", self::TTL_LOOKUP, function() use ($slug) {
            return City::where('slug', $slug)->first();
        });
    }

    /**
     * Get recent reviews for sidebar
     */
    public static function getRecentReviews($limit = 10)
    {
        return Cache::remember("cache:recent_reviews:{$limit}", self::TTL_REVIEWS, function() use ($limit) {
            return Review::select('id', 'review', 'user_id', 'profile_id', 'created_at')
                ->with([
                    'getuser:id,name,slug',
                    'getpic:id,user_id,profile_id,image'
                ])
                ->where('status', 'approved')
                ->latest()
                ->take($limit)
                ->get();
        });
    }

    /**
     * Get profile with all related data (for profile detail page).
     * Versioned: invalidates automatically when the profile, its images, or its reviews change.
     */
    public static function getProfileDetail($profileId)
    {
        $scope = CacheVersion::profileScope($profileId);
        return CacheVersion::remember($scope, 'detail', self::TTL_PROFILE_DETAIL, function() use ($profileId) {
            return UsersProfile::with([
                'user:id,name,email,type',
                'singleimg',
                'coverimg',
                'multipleimgs',
                'photoverify:id,profile_id,status',
                'services',
                'languages',
                'package:id,name',
                'gcity:id,name,country',
                'ggender:id,name',
                'gbust:id,name',
                'ethi:id,name',
                'gnat:id,nicename',
                'ghair:id,name',
                'ori:id,name',
            ])->find($profileId);
        });
    }

    public static function getProfileImages($profileId)
    {
        $scope = CacheVersion::profileScope($profileId);
        return CacheVersion::remember($scope, 'images', self::TTL_PROFILE_DETAIL, function() use ($profileId) {
            return \App\Models\ProfileImage::where('profile_id', $profileId)
                ->orderBy('id', 'asc')
                ->get();
        });
    }

    public static function getProfileReviews($profileId)
    {
        $scope = CacheVersion::profileScope($profileId);
        return CacheVersion::remember($scope, 'reviews', self::TTL_REVIEWS, function() use ($profileId) {
            return Review::where('profile_id', $profileId)
                ->where('status', 'approved')
                ->with('getuser:id,name')
                ->latest()
                ->get();
        });
    }

    public static function getProfileQuestions($profileId)
    {
        $scope = CacheVersion::profileScope($profileId);
        return CacheVersion::remember($scope, 'questions', self::TTL_REVIEWS, function() use ($profileId) {
            return \App\Models\Question::where('profile_id', $profileId)
                ->where('status', 1)
                ->latest()
                ->get();
        });
    }

    // =====================
    // Cache Invalidation
    // =====================

    /**
     * Clear all profile-related caches for a specific profile.
     * With version-based keys the old entries simply become unreachable
     * (they expire naturally). Observers should call CacheVersion::bump()
     * directly; this method exists for ad-hoc admin flushes.
     */
    public static function clearProfileCache($profileId)
    {
        CacheVersion::bump(CacheVersion::profileScope($profileId));
    }

    /**
     * Invalidate homepage listing for a city/gender scope plus some related
     * non-versioned caches (recent reviews, per-request auction cache).
     */
    public static function clearHomepageCache($cityId = null, $gender = null)
    {
        Cache::forget('cache:recent_reviews:10');
        Cache::forget('cache:recent_reviews:30');

        if ($cityId && $gender) {
            Cache::forget("auctions:{$cityId}:{$gender}:auth");
            Cache::forget("auctions:{$cityId}:{$gender}:guest");
            // Legacy numeric auth key names kept for backward compatibility:
            Cache::forget("auctions:{$cityId}:{$gender}:1");
            Cache::forget("auctions:{$cityId}:{$gender}:0");
        }

        CacheVersion::bump(CacheVersion::listingScope($cityId, $gender));
    }

    /**
     * Clear all lookup caches (call this after admin updates)
     */
    public static function clearLookupCaches()
    {
        Cache::forget('cache:services');
        Cache::forget('cache:currencies');
        Cache::forget('cache:ethnicities');
        Cache::forget('cache:busts');
        Cache::forget('cache:haircolors');
        Cache::forget('cache:countries');
        Cache::forget('cache:languages');
        Cache::forget('cache:packages');
        Cache::forget('cache:package_ids_by_type');
    }

    /**
     * Clear all caches (nuclear option)
     */
    public static function clearAllCaches()
    {
        Cache::flush();
    }
}
