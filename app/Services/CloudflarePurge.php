<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cloudflare edge cache invalidation.
 *
 * Designed for the **Pro plan** — uses URL-based purge only (no prefix/tag purge,
 * those are Enterprise-only). Methods that would call Enterprise APIs fall back to
 * enumerating likely URLs and purging them individually.
 *
 * Used by model observers after they bump the origin CacheVersion so the edge
 * cache matches the new content immediately instead of waiting for TTL.
 *
 * All methods are no-ops if the integration is not configured — safe to call from
 * observers that may run in dev/testing environments.
 */
class CloudflarePurge
{
    protected const API_BASE = 'https://api.cloudflare.com/client/v4';

    /** Purge specific URLs from the edge. Up to 30 URLs per call on Pro plan. */
    public static function purgeUrls(array $urls): bool
    {
        $urls = array_values(array_unique(array_filter($urls)));
        if (empty($urls) || !self::isEnabled()) {
            return false;
        }

        $ok = true;
        foreach (array_chunk($urls, 30) as $chunk) {
            $ok = self::call(['files' => $chunk]) && $ok;
        }
        return $ok;
    }

    /** Nuclear: purge entire zone. Use only from admin actions. */
    public static function purgeEverything(): bool
    {
        if (!self::isEnabled()) {
            return false;
        }
        return self::call(['purge_everything' => true]);
    }

    // --- Convenience helpers that match application semantics ---

    /** Invalidate a single profile's detail page + the listing root for that city+gender. */
    public static function purgeProfileUrls(int $profileId, ?string $genderName = null, ?string $citySlug = null): bool
    {
        if (!$genderName || !$citySlug) {
            return false;
        }

        $base = rtrim(self::siteUrl(), '/');
        $urls = self::buildListingUrls($base, $genderName, $citySlug);
        // Detail page (slug unknown — purge first few common variants if we have it elsewhere)
        // Actual detail URL varies by slug — we mainly target the listing tree.
        return self::purgeUrls($urls);
    }

    /**
     * Invalidate all listing pages for a city+gender scope.
     * Enumerates the listing root + the first N pagination URLs since prefix
     * purge isn't available on Pro.
     */
    public static function purgeListing(string $genderName, string $citySlug, int $maxPages = 5): bool
    {
        $base = rtrim(self::siteUrl(), '/');
        return self::purgeUrls(self::buildListingUrls($base, $genderName, $citySlug, $maxPages));
    }

    /** Purge the home + a specific profile detail URL by id+slug. */
    public static function purgeDetail(string $genderName, string $citySlug, int $profileId, string $profileSlug): bool
    {
        $base = rtrim(self::siteUrl(), '/');
        return self::purgeUrls([
            "{$base}/{$genderName}-escorts-in-{$citySlug}/{$profileId}/{$profileSlug}",
        ]);
    }

    /** Build the set of listing URLs for a city+gender scope. */
    protected static function buildListingUrls(string $base, string $gender, string $city, int $pages = 5): array
    {
        $urls = [
            "{$base}/{$gender}-escorts-in-{$city}",
            "{$base}/{$gender}-escorts-in-{$city}/",
        ];
        for ($p = 2; $p <= $pages; $p++) {
            $urls[] = "{$base}/{$gender}-escorts-in-{$city}/page/{$p}";
        }
        return $urls;
    }

    // --- Internals ---

    protected static function call(array $payload): bool
    {
        $zone = config('services.cloudflare.zone_id');
        $token = config('services.cloudflare.api_token');

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(5)
                ->post(self::API_BASE . "/zones/{$zone}/purge_cache", $payload);

            if ($response->successful() && ($response->json('success') === true)) {
                return true;
            }

            Log::warning('Cloudflare purge failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Cloudflare purge exception: ' . $e->getMessage(), ['payload' => $payload]);
        }

        return false;
    }

    protected static function isEnabled(): bool
    {
        if (!config('services.cloudflare.enabled')) {
            return false;
        }
        if (!config('services.cloudflare.api_token') || !config('services.cloudflare.zone_id')) {
            return false;
        }
        return true;
    }

    protected static function siteUrl(): string
    {
        return (string) (config('services.cloudflare.site_url') ?: config('app.url') ?: url('/'));
    }
}
