<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cloudflare edge cache invalidation.
 *
 * Used by model observers after they bump the origin CacheVersion so the edge
 * cache matches the new content immediately instead of waiting for TTL.
 *
 * Three purge modes are exposed:
 *   - purgeUrls($urls)    — by exact URL (best for point updates)
 *   - purgeByPrefix($prefix) — Enterprise feature, one call purges an entire path tree
 *   - purgeEverything()   — nuclear, use sparingly
 *
 * All methods are no-ops if the integration is not configured — safe to call from
 * observers that may run in dev/testing environments.
 */
class CloudflarePurge
{
    protected const API_BASE = 'https://api.cloudflare.com/client/v4';

    /** Purge specific URLs from the edge. Up to 30 per call on Free/Pro/Business, 500 on Enterprise. */
    public static function purgeUrls(array $urls): bool
    {
        $urls = array_values(array_unique(array_filter($urls)));
        if (empty($urls) || !self::isEnabled()) {
            return false;
        }

        // Chunk to 30 to stay within the lowest plan limit (Enterprise supports up to 500).
        $ok = true;
        foreach (array_chunk($urls, 30) as $chunk) {
            $ok = self::call(['files' => $chunk]) && $ok;
        }
        return $ok;
    }

    /**
     * Purge everything under a URL prefix (Enterprise only).
     * e.g. purgeByPrefix('evoory.com/female-escorts-in-dubai/')
     */
    public static function purgeByPrefix(string $prefix): bool
    {
        if (!self::isEnabled()) {
            return false;
        }
        return self::call(['prefixes' => [ltrim($prefix, 'https://')]]);
    }

    /**
     * Purge by cache tag (Enterprise only). Requires Cache-Tag header set on origin responses.
     * e.g. purgeByTag('listing:229:1') after bumping that scope.
     */
    public static function purgeByTag(string|array $tags): bool
    {
        $tags = is_array($tags) ? $tags : [$tags];
        $tags = array_values(array_unique(array_filter($tags)));
        if (empty($tags) || !self::isEnabled()) {
            return false;
        }
        return self::call(['tags' => $tags]);
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

    /** Invalidate a profile's detail page + the listing page variants that include it. */
    public static function purgeProfileUrls(int $profileId, ?string $genderName = null, ?string $citySlug = null): bool
    {
        $base = rtrim(self::siteUrl(), '/');
        $urls = [];

        if ($genderName && $citySlug) {
            // Exact detail URL is unknown (slug varies), so purge the listing tree for that city+gender.
            // With Enterprise, this one prefix covers pagination, filters, and the detail page.
            return self::purgeByPrefix("{$base}/{$genderName}-escorts-in-{$citySlug}/");
        }

        // Fallback (non-Enterprise or missing info): best-effort — purge the whole listing tree.
        return self::purgeByPrefix("{$base}/");
    }

    /** Invalidate all listing pages for a specific city+gender scope. */
    public static function purgeListing(string $genderName, string $citySlug): bool
    {
        $base = rtrim(self::siteUrl(), '/');
        return self::purgeByPrefix("{$base}/{$genderName}-escorts-in-{$citySlug}");
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
