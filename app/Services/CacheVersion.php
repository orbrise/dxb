<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Version-based cache invalidation.
 *
 * Each "scope" (e.g. "listing:229:1", "profile:42") has a monotonically
 * increasing version counter stored in cache. Cache keys embed the current
 * version, so bumping the version instantly orphans every cached entry in
 * that scope — they expire naturally via TTL without a scan/flush.
 *
 * Read:   versioned('profile:42', 'detail') → "cache:v3:profile:42:detail"
 * Bump:   bump('profile:42')                 → v3 becomes v4, old keys orphaned
 */
class CacheVersion
{
    /** Get the current version number for a scope. Initializes to 1. */
    public static function get(string $scope): int
    {
        return (int) Cache::rememberForever(self::versionKey($scope), fn () => 1);
    }

    /** Increment the scope's version. Returns the new version. */
    public static function bump(string $scope): int
    {
        $key = self::versionKey($scope);
        if (!Cache::has($key)) {
            Cache::forever($key, 1);
        }
        $new = Cache::increment($key);
        return is_numeric($new) ? (int) $new : self::get($scope);
    }

    /** Bump multiple scopes at once (deduped). */
    public static function bumpMany(array $scopes): void
    {
        foreach (array_unique(array_filter($scopes)) as $scope) {
            self::bump($scope);
        }
    }

    /** Build a versioned cache key. $scope must match what bump() receives. */
    public static function versioned(string $scope, string $subkey): string
    {
        return 'cache:v' . self::get($scope) . ":{$scope}:{$subkey}";
    }

    /** remember() helper: read-through cache keyed by current scope version. */
    public static function remember(string $scope, string $subkey, int $ttl, \Closure $callback)
    {
        return Cache::remember(self::versioned($scope, $subkey), $ttl, $callback);
    }

    protected static function versionKey(string $scope): string
    {
        return "cache:version:{$scope}";
    }

    // --- Scope builders (use these instead of raw strings so callers stay consistent) ---

    public static function listingScope($cityId, $genderId): string
    {
        $c = $cityId !== null && $cityId !== '' ? $cityId : 'any';
        $g = $genderId !== null && $genderId !== '' ? $genderId : 'any';
        return "listing:{$c}:{$g}";
    }

    public static function profileScope($profileId): string
    {
        return "profile:{$profileId}";
    }

    public static function lookupScope(): string
    {
        return 'lookups';
    }

    public static function auctionScope($cityId, $gender): string
    {
        $c = $cityId !== null && $cityId !== '' ? $cityId : 'any';
        $g = $gender !== null && $gender !== '' ? $gender : 'any';
        return "auctions:{$c}:{$g}";
    }

    public static function pageScope(): string
    {
        return 'pages';
    }
}
