<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Debug-only timing middleware. Adds a Server-Timing response header and
 * X-Debug-Perf header so we can see — in Chrome DevTools → Network → the
 * document request's Response Headers — exactly how long PHP spent on each
 * phase of the request plus how many SQL queries ran.
 *
 * Only runs when ?perf=1 is on the URL so it never costs anything in prod
 * for normal visitors.
 *
 * Usage: open https://evoory.com/female-escorts-in-dubai?perf=1 and read
 * the Server-Timing / X-Debug-Perf response headers.
 */
class ServerTiming
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->query('perf')) {
            return $next($request);
        }

        $t0 = microtime(true);
        $queryCount = 0;
        $queryTimeMs = 0.0;
        DB::listen(function ($q) use (&$queryCount, &$queryTimeMs) {
            $queryCount++;
            $queryTimeMs += $q->time;
        });

        $response = $next($request);

        $totalMs = (microtime(true) - $t0) * 1000;
        $renderMs = $totalMs - $queryTimeMs;

        $response->headers->set(
            'Server-Timing',
            sprintf(
                'db;dur=%.1f;desc="%d queries", render;dur=%.1f, total;dur=%.1f',
                $queryTimeMs, $queryCount, $renderMs, $totalMs
            )
        );
        $response->headers->set(
            'X-Debug-Perf',
            sprintf('total=%.0fms db=%.0fms queries=%d', $totalMs, $queryTimeMs, $queryCount)
        );

        return $response;
    }
}
