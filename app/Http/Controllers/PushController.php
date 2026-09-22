<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * Public VAPID key for the browser to bind its subscription to. Safe to
     * expose — the private half stays server-side (see /rtc/push send path).
     * Returns empty when not configured so the client skips the whole
     * subscribe flow gracefully instead of erroring out.
     */
    public function vapidPublicKey()
    {
        return response()->json([
            'publicKey' => config('services.push.vapid_public') ?: null,
        ])->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Save (or upsert) a browser's PushSubscription so we can wake it up
     * with an incoming-call notification later. Called from the client
     * immediately after the user grants notification permission.
     */
    public function subscribe(Request $request)
    {
        abort_unless($request->user(), 401);

        $data = $request->validate([
            'endpoint'        => 'required|string|max:512',
            'keys.p256dh'     => 'required|string|max:255',
            'keys.auth'       => 'required|string|max:100',
            'contentEncoding' => 'nullable|string|max:20',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id'          => $request->user()->id,
                'p256dh_key'       => $data['keys']['p256dh'],
                'auth_key'         => $data['keys']['auth'],
                'content_encoding' => $data['contentEncoding'] ?? null,
                'user_agent'       => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Remove a subscription — called when the user revokes permission or
     * unregisters the service worker. Also silently invoked by the send
     * path when the push service returns 410 Gone.
     */
    public function unsubscribe(Request $request)
    {
        abort_unless($request->user(), 401);

        $data = $request->validate([
            'endpoint' => 'required|string|max:512',
        ]);

        PushSubscription::where('endpoint', $data['endpoint'])
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['ok' => true]);
    }
}
