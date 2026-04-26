<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Strips Set-Cookie headers from responses that are marked edge-cacheable.
 *
 * Cloudflare (and most CDNs) automatically bypass the cache for any response that
 * contains a Set-Cookie header — they assume the response is personalized. Laravel's
 * StartSession middleware adds `laravel_session` and `XSRF-TOKEN` cookies on EVERY
 * response, including ones our CachePageResponse middleware marked public, which means
 * Cloudflare silently refuses to cache them and we hit the origin every time.
 *
 * This middleware runs LAST on the response path (registered first in the kernel's
 * global stack so the response unwinds through it last) and removes Set-Cookie when
 * the response declares `CDN-Cache-Control: public`. Anonymous browsing pages don't
 * need a session cookie — Laravel will start a fresh session on the next interaction
 * (login click, form submit), at which point that response is uncached and gets normal
 * cookies.
 */
class StripCookiesForEdgeCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$this->isEdgeCacheable($response)) {
            return $response;
        }

        // Remove every Set-Cookie. Laravel will reissue them on the next non-cacheable
        // request (login form, profile edit, etc.) when a session is actually needed.
        $response->headers->remove('Set-Cookie');
        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->removeCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
        }

        // PHP's session.cache_limiter (default: "nocache") adds Cache-Control: no-store,
        // no-cache, private + Pragma: no-cache on any response that touched a session.
        // Restore the public Cache-Control we set in CachePageResponse::markPublic().
        $cdn = (string) $response->headers->get('CDN-Cache-Control', '');
        if (preg_match('/max-age=(\d+)/', $cdn, $m)) {
            $edge = (int) $m[1];
            $browser = max(60, (int) ($edge / 60)); // sane default browser TTL
            $response->headers->set('Cache-Control', "public, max-age={$browser}, s-maxage={$edge}");
            $response->headers->remove('Pragma');
            $response->headers->remove('Expires');
        }

        return $response;
    }

    protected function isEdgeCacheable(Response $response): bool
    {
        $cdnCacheControl = (string) $response->headers->get('CDN-Cache-Control', '');
        if (str_contains($cdnCacheControl, 'public') && !str_contains($cdnCacheControl, 'private') && !str_contains($cdnCacheControl, 'no-store')) {
            return true;
        }

        // Fallback: also recognize regular Cache-Control: public, s-maxage=N
        $cacheControl = (string) $response->headers->get('Cache-Control', '');
        if (str_contains($cacheControl, 'public') && str_contains($cacheControl, 's-maxage=') && !str_contains($cacheControl, 'private')) {
            return true;
        }

        return false;
    }
}
