<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Bump auth()->user()->last_seen_at on each authenticated request so the
 * chat UI can render "online" / "last seen X ago" status.
 *
 * Debounced to once per minute per user via a cache guard — otherwise every
 * single page view + Livewire poll (every 8s on the chat page) would write
 * to the users table, hammering the DB. One minute of latency on presence
 * is invisible; one write per minute per active user is cheap.
 */
class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $userId = auth()->id();
            $cacheKey = "user.last_seen_tick.{$userId}";

            if (!Cache::has($cacheKey)) {
                // Direct UPDATE, no model load/save — skips observers,
                // doesn't bump updated_at, no event dispatch.
                DB::table('users')->where('id', $userId)->update(['last_seen_at' => now()]);
                Cache::put($cacheKey, 1, 60);
            }
        }

        return $next($request);
    }
}
