<?php

namespace App\Http\Middleware;

use App\Services\CacheVersion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Two-layer HTML cache:
 *  - Origin layer: caches the rendered body in Redis/file cache (keyed by versioned pageScope).
 *    Observer bumps of CacheVersion::listingScope() orphan cached entries for affected city/gender combinations.
 *  - Edge layer: emits Cache-Control / CDN-Cache-Control / s-maxage / Surrogate-Control so
 *    Cloudflare and any upstream CDN cache the response at the edge. The separate
 *    CDN-Cache-Control lets us use a long edge TTL while keeping a shorter browser TTL.
 *
 * Bypass rules:
 *  - Non-GET requests
 *  - JSON/XHR requests
 *  - Authenticated users (they see personalized state)
 *  - Requests with an existing session cookie that identifies the user
 *  - Requests with _token / preview query params
 *  - Responses that issue set-cookie (login, flash) — never cache those
 */
class CachePageResponse
{
    /** Origin cache TTL — safety ceiling. Actual invalidation is observer-driven via listingScope version. */
    protected const ORIGIN_TTL_SECONDS = 3600;

    /** Edge TTL for CDN. Can be much longer than origin TTL because CDN gets purged on content change. */
    protected const EDGE_TTL_SECONDS = 7200;

    /** Browser TTL — keep short so users see admin-driven updates without a hard reload. */
    protected const BROWSER_TTL_SECONDS = 300;

    public function handle(Request $request, Closure $next)
    {
        if (!$this->shouldCacheRequest($request)) {
            $response = $next($request);
            $this->markPrivate($response);
            return $response;
        }

        $cacheKey = $this->cacheKey($request);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            // Orphan stale entries that should never have been written:
            //  - empty body (e.g. an old HEAD response that snuck past the request filter)
            //  - body with Livewire content (would 419 on first wire:submit)
            $cachedBody = $cached['body'] ?? '';
            if ($cachedBody === '' || $this->bodyHasLivewire($cachedBody)) {
                Cache::forget($cacheKey);
            } else {
                $response = response($cached['body'], $cached['status'], $cached['headers']);
                $response->headers->set('X-Page-Cache', 'HIT');
                $this->markPublic($response);

                // Livewire injects its runtime (<style> + <script> with per-session CSRF) via
                // a RequestHandled listener that normally only fires when a component is rendered.
                // On a cache HIT no component renders, so without this flag the cached page ships
                // without Livewire → nothing interactive, layout-breaking missing styles.
                // Forcing injection makes Livewire inject fresh assets on every cached response.
                if (class_exists(\Livewire\Features\SupportAutoInjectedAssets\SupportAutoInjectedAssets::class)) {
                    \Livewire\Features\SupportAutoInjectedAssets\SupportAutoInjectedAssets::$forceAssetInjection = true;
                }

                return $response;
            }
        }

        $response = $next($request);

        if ($this->shouldCacheResponse($response)) {
            $this->storeResponse($cacheKey, $response);
            $response->headers->set('X-Page-Cache', 'MISS');
            $this->markPublic($response);
        } else {
            $this->markPrivate($response);
        }

        return $response;
    }

    protected function shouldCacheRequest(Request $request): bool
    {
        // GET only. HEAD responses have empty bodies in Symfony; caching one and
        // serving it back to a GET produces a Content-Length: 0 page.
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        // Livewire/internal hits — these should not be edge-cached.
        if ($request->header('X-Livewire') || str_starts_with($request->path(), 'livewire/')) {
            return false;
        }

        if ($request->query->has('_token') || $request->query->has('preview')) {
            return false;
        }

        if (auth()->check()) {
            return false;
        }

        return true;
    }

    protected function shouldCacheResponse(Response $response): bool
    {
        if ($response->getStatusCode() !== 200) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type', '');
        if (stripos($contentType, 'text/html') === false) {
            return false;
        }

        // Never cache anything that sets cookies (would leak state across users).
        if ($response->headers->has('set-cookie')) {
            return false;
        }

        $body = (string) $response->getContent();

        // Defensive: never cache an empty body. Some upstream paths (HEAD handling,
        // streamed responses, content-emptied error renders) can leave getContent()
        // empty even when status=200, and a cached blank page is worse than a miss.
        if ($body === '') {
            return false;
        }

        // Pages with interactive Livewire components embed a per-session CSRF token
        // and an encrypted wire:snapshot. Caching freezes both, so any visitor who
        // gets the cached HTML will hit a 419 on their first wire:submit/wire:click.
        if ($this->bodyHasLivewire($body)) {
            return false;
        }

        return true;
    }

    protected function bodyHasLivewire(string $body): bool
    {
        if ($body === '') {
            return false;
        }

        return str_contains($body, 'wire:snapshot')
            || str_contains($body, 'wire:submit')
            || str_contains($body, 'wire:click')
            || str_contains($body, 'wire:model');
    }

    protected function cacheKey(Request $request): string
    {
        // Extract city and gender from URL for more granular caching
        $cityId = $this->extractCityFromUrl($request);
        $gender = $this->extractGenderFromUrl($request);
        
        // Use city+gender specific scope instead of global pageScope
        $scope = CacheVersion::listingScope($cityId, $gender);
        
        // fullUrl includes query string, so different filters get different keys.
        // Version the key off listingScope so only relevant pages get invalidated.
        return CacheVersion::versioned(
            $scope,
            'page:html:' . md5($request->fullUrl())
        );
    }

    protected function extractCityFromUrl(Request $request): ?int
    {
        // Match patterns like /female-escorts-in-dubai or /female-escorts-in-dubai/page/2
        $path = $request->path();
        
        if (preg_match('/^\w+-escorts-in-([\w-]+)(?:\/(?:page\/\d+|\d+\/[^\/]+))?$/', $path, $matches)) {
            $citySlug = $matches[1];
            // Use CacheService for consistent city lookups
            $city = \App\Services\CacheService::getCityBySlug($citySlug);
            return $city ? $city->id : null;
        }
        
        return null;
    }

    protected function extractGenderFromUrl(Request $request): ?int
    {
        // Match patterns like /female-escorts-in-dubai
        $path = $request->path();
        
        if (preg_match('/^(\w+)-escorts-in-/', $path, $matches)) {
            $genderName = $matches[1];
            // Use CacheService for consistent gender lookups
            $gender = \App\Services\CacheService::getGenderByName($genderName);
            return $gender ? $gender->id : null;
        }
        
        return null;
    }

    protected function storeResponse(string $cacheKey, Response $response): void
    {
        // Don't persist the Cache-Control header we emit at runtime — it's set on each response.
        $headers = collect($response->headers->allPreserveCase())
            ->reject(fn ($values, $key) => in_array(strtolower($key), [
                'cache-control', 'cdn-cache-control', 'surrogate-control', 'x-page-cache',
            ], true))
            ->toArray();

        Cache::put($cacheKey, [
            'status' => $response->getStatusCode(),
            'headers' => $headers,
            'body' => $response->getContent(),
        ], self::ORIGIN_TTL_SECONDS);
    }

    /**
     * Public edge-cacheable response.
     *
     *   Cache-Control: public, max-age=60, s-maxage=3600
     *   CDN-Cache-Control: public, max-age=3600       (Cloudflare-specific override)
     *   Surrogate-Control: max-age=3600                (Varnish / other surrogate caches)
     *   Vary: Accept-Encoding                          (so brotli/gzip don't collide with identity)
     */
    protected function markPublic(Response $response): void
    {
        $browser = self::BROWSER_TTL_SECONDS;
        $edge = self::EDGE_TTL_SECONDS;

        $response->headers->set('Cache-Control', "public, max-age={$browser}, s-maxage={$edge}");
        $response->headers->set('CDN-Cache-Control', "public, max-age={$edge}");
        $response->headers->set('Surrogate-Control', "max-age={$edge}");
        $response->headers->set('Vary', 'Accept-Encoding');
    }

    /**
     * Private (user-specific) response — must NEVER be cached at edge or by shared caches.
     */
    protected function markPrivate(Response $response): void
    {
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        $response->headers->remove('CDN-Cache-Control');
        $response->headers->remove('Surrogate-Control');
    }
}
