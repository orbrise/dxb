<?php

namespace App\Broadcasting;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Support\Facades\Cache;

/**
 * The Reverb broadcaster, but it stops trying for a while once the Reverb
 * server doesn't answer.
 *
 * While Reverb is down, every broadcast waits out the client connect
 * timeout (about 1 s) before failing. Pages here don't depend on the socket
 * (they poll), but the wait still lands on the request: a customer's chat
 * refresh broadcasts both "delivered" and "read", so a reply took 2+ s longer
 * to show. After a connection failure we skip broadcasts for PAUSE_SECONDS;
 * then a single request tries again (the rest keep skipping meanwhile), and
 * broadcasting resumes as soon as Reverb answers.
 */
class CircuitBreakingPusherBroadcaster extends PusherBroadcaster
{
    protected const PAUSE_KEY = 'broadcast:reverb-paused-until';
    protected const PROBE_KEY = 'broadcast:reverb-probe';
    // The retry itself waits out the timeout, so don't do it too often.
    protected const PAUSE_SECONDS = 60;

    public function broadcast(array $channels, $event, array $payload = [])
    {
        $pausedUntil = (int) Cache::get(static::PAUSE_KEY, 0);
        if ($pausedUntil && (time() < $pausedUntil || !Cache::add(static::PROBE_KEY, 1, 5))) {
            return;
        }

        try {
            parent::broadcast($channels, $event, $payload);
        } catch (BroadcastException $e) {
            // Connection-level failures only (can't connect, timeout, TLS,
            // reset) — not e.g. an auth error from a running server.
            if (preg_match('/cURL error (6|7|28|35|52|56)\b/', $e->getMessage())) {
                Cache::put(static::PAUSE_KEY, time() + static::PAUSE_SECONDS, 3600);
            }
            throw $e;
        }

        if ($pausedUntil) Cache::forget(static::PAUSE_KEY);
    }
}
