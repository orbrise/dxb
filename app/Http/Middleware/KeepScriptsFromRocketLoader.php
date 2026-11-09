<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Production sits behind Cloudflare with Rocket Loader on. It rewrites every
 * <script> to a made-up type ("…-text/javascript") and runs them itself after
 * a full page load. wire:navigate swaps pages in without a load, and Livewire
 * re-runs the new page's scripts by cloning them, type included — so the
 * browser skips every one: on production no inline script ran on a page
 * reached with wire:navigate (e.g. the homepage city search after visiting
 * chat), while locally everything worked.
 *
 * data-cfasync="false" tells Rocket Loader to leave a script alone, so pages
 * run their scripts on production exactly as they do locally. Livewire's own
 * injected <script> gets the same attribute in AppServiceProvider.
 */
class KeepScriptsFromRocketLoader
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();
        if (!is_string($html) || stripos($html, '<script') === false) {
            return $response;
        }

        // Whole <script>…</script> blocks, so a "<script" inside a JS string is
        // never touched; same linear-time pattern as MinifyHtml.
        $tagged = preg_replace_callback(
            '/<script\b([^>]*+)>[^<]*+(?:<(?!\/script>)[^<]*+)*+<\/script>/i',
            function ($m) {
                if (stripos($m[1], 'data-cfasync') !== false) {
                    return $m[0];
                }
                $openingTag = strlen('<script' . $m[1] . '>');
                return '<script data-cfasync="false"' . $m[1] . '>' . substr($m[0], $openingTag);
            },
            $html
        );

        // null = a PCRE limit was hit; serve the page untouched.
        if ($tagged !== null) {
            $response->setContent($tagged);
        }

        return $response;
    }
}
