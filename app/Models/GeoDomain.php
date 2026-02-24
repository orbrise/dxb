<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeoDomain extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'domain',
        'is_active',
        'priority',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get active domain for a country code
     */
    public static function getActiveDomainForCountry(string $countryCode): ?string
    {
        return Cache::remember("geo_domain_{$countryCode}", 3600, function () use ($countryCode) {
            $domain = static::where('country_code', strtoupper($countryCode))
                ->where('is_active', true)
                ->first();
            
            return $domain?->domain;
        });
    }

    /**
     * Get country code from domain
     */
    public static function getCountryFromDomain(string $domain): ?string
    {
        return Cache::remember("geo_country_{$domain}", 3600, function () use ($domain) {
            $geoDomain = static::where('domain', $domain)->first();
            return $geoDomain?->country_code;
        });
    }

    /**
     * Check if domain is active
     */
    public static function isDomainActive(string $domain): bool
    {
        return Cache::remember("geo_domain_active_{$domain}", 3600, function () use ($domain) {
            return static::where('domain', $domain)
                ->where('is_active', true)
                ->exists();
        });
    }

    /**
     * Get all active domains
     */
    public static function getAllActiveDomains(): array
    {
        return Cache::remember('geo_active_domains', 3600, function () {
            return static::where('is_active', true)
                ->pluck('domain', 'country_code')
                ->toArray();
        });
    }

    /**
     * Clear geo domain cache
     */
    public static function clearCache(): void
    {
        $domains = static::all();
        
        foreach ($domains as $domain) {
            Cache::forget("geo_domain_{$domain->country_code}");
            Cache::forget("geo_country_{$domain->domain}");
            Cache::forget("geo_domain_active_{$domain->domain}");
        }
        
        Cache::forget('geo_active_domains');
    }

    /**
     * Boot method to clear cache on changes
     */
    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
