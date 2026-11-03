<?php

namespace App\Http\Controllers;

use App\Events\CallSignal;
use App\Events\NewChatMessage;
use App\Models\Call;
use App\Models\CallSignal as CallSignalRow;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{
    /**
     * Single signaling endpoint used for the whole WebRTC handshake:
     *  - offer:    caller → callee (creates the Call row)
     *  - ringing:  callee → caller (acknowledges the ring)
     *  - answer:   callee → caller (call becomes answered)
     *  - ice:      either side → other (candidate trickle)
     *  - decline:  callee → caller (rejected)
     *  - hangup:   either → other (end call)
     *
     * Signal payload is opaque to the server; it just persists lifecycle
     * transitions and relays the message via CallSignal broadcast.
     */
    public function signal(Request $request)
    {
        $rtc = Log::channel('rtc');
        $user = $request->user();

        // Release the session file lock immediately so concurrent requests
        // from the same browser session (polls, livewire updates) don't
        // serialize behind this one. We only read $request->user() here;
        // nothing writes to session.
        if ($request->hasSession()) {
            $request->session()->save();
        }

        $rtc->info('signal.entered', [
            'auth_id' => $user?->id,
            'ip' => $request->ip(),
            'type' => $request->input('type'),
            'call_id' => $request->input('call_id'),
            'target' => $request->input('target_user_id'),
        ]);

        abort_unless($user, 401);

        try {
            $data = $request->validate([
                // target_user_id becomes optional on the initial support-call
                // offer (is_support=1 + target_user_id missing), because the
                // call hasn't been claimed by a specific admin yet. All other
                // signal types still require a concrete target.
                'target_user_id' => 'nullable|integer|exists:users,id',
                'call_id'        => 'required|string|min:8|max:40',
                'type'           => 'required|string|in:offer,answer,ice,hangup,decline,ringing,switch-request,switch-accept,switch-reject',
                'call_type'      => 'required|string|in:audio,video',
                'payload'        => 'nullable|array',
                'conversation_id'=> 'nullable|integer|exists:conversations,id',
                'is_support'     => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $rtc->warning('signal.validation_failed', [
                'errors' => $ve->errors(),
                'input' => $request->except(['payload']),
            ]);
            throw $ve;
        }

        $isSupport = (bool) ($data['is_support'] ?? false);
        $targetId = (int) ($data['target_user_id'] ?? 0);

        // Support-offer validation: on the initial offer the caller doesn't
        // know which admin will accept, so we accept target_user_id=0 and
        // broadcast on support-inbox instead. For every other signal (ice,
        // answer, hangup, …) the admin has already been determined so a
        // real target is required.
        if ($data['type'] !== 'offer' || !$isSupport) {
            if ($targetId <= 0) {
                return response()->json(['error' => 'target_user_id required'], 422);
            }
        }

        if ($targetId > 0 && $targetId === $user->id) {
            $rtc->warning('signal.self_call_rejected', ['auth_id' => $user->id]);
            return response()->json(['error' => 'cannot call yourself'], 422);
        }

        // Block guard: silently drop signals in either direction of a block
        // relationship. Only reject on initial offer so late ice/hangup from
        // an in-progress call don't error out if someone blocks mid-call.
        // Support offers skip this check — the caller isn't picking a peer.
        if ($data['type'] === 'offer' && !$isSupport && $user->isBlockRelationWith($targetId)) {
            $rtc->info('signal.blocked_rejected', ['auth_id' => $user->id, 'target' => $targetId]);
            return response()->json(['error' => 'blocked'], 403);
        }

        // Moderation: support staff can bar a customer from calling at all.
        // Deliberately NOT gated on !$isSupport like the peer-block guard above
        // — a support offer is the one path a blocked customer keeps retrying,
        // and it has no concrete target to check a block relation against.
        if ($data['type'] === 'offer' && $user->calls_blocked) {
            $rtc->info('signal.calls_blocked_rejected', [
                'auth_id' => $user->id,
                'support' => $isSupport,
            ]);
            return response()->json(['error' => 'calls_blocked'], 403);
        }

        if ($data['type'] === 'offer' && $isSupport
            && Setting::query()->whereKey(1)->value('support_calls_blocked')) {
            $rtc->info('signal.support_calls_blocked_rejected', ['auth_id' => $user->id]);
            return response()->json(['error' => 'support_calls_blocked'], 403);
        }

        try {
            $this->recordLifecycle(
                $data['call_id'],
                $user->id,
                $targetId,
                $data['type'],
                $data['call_type'],
                $data['conversation_id'] ?? null,
                $isSupport
            );
        } catch (\Throwable $e) {
            $rtc->error('signal.lifecycle_failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            // Continue anyway — history is nice-to-have, delivery matters.
        }

        // Always persist the signal to the durable queue. The callee polls
        // this table every ~1.5s from the chat page. This means calls work
        // even if Reverb is unreachable or dropping events.
        // For support offers, to_user_id is 0 (shared inbox) — the row still
        // records the lifecycle. Admins don't poll the queue for pending
        // claims, they rely on the broadcast on support-inbox.
        try {
            $row = CallSignalRow::create([
                'call_id' => $data['call_id'],
                'from_user_id' => $user->id,
                'to_user_id' => $targetId,
                'type' => $data['type'],
                'call_type' => $data['call_type'],
                'payload' => $data['payload'] ?? [],
                'created_at' => now(),
            ]);
            $rtc->info('signal.queued', [
                'row_id' => $row->id,
                'call_id' => $data['call_id'],
                'from' => $user->id,
                'to' => $targetId,
                'type' => $data['type'],
                'payload_bytes' => strlen(json_encode($data['payload'] ?? [])),
            ]);
        } catch (\Throwable $e) {
            // If this fails (e.g. call_signals table doesn't exist yet, no
            // migrate), we log loud so the user sees it — the whole
            // polling-fallback path depends on this row landing.
            $rtc->error('signal.queue_insert_failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'sql_state' => $e instanceof \PDOException ? ($e->errorInfo[0] ?? null) : null,
            ]);
            return response()->json([
                'error' => 'queue insert failed',
                'detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        // Best-effort real-time broadcast on top of the polling queue —
        // when Reverb works, delivery is near-instant; when it doesn't, the
        // polling path picks it up a moment later. Support offers fan out
        // on the shared support-inbox channel (CallSignal::broadcastOn adds
        // the extra channel when isSupport=true + targetId=0).
        try {
            broadcast(new CallSignal(
                $targetId,
                $user->id,
                $data['call_id'],
                $data['type'],
                $data['call_type'],
                $data['payload'] ?? [],
                $row->id ?? null,
                $isSupport
            ))->toOthers();
            $rtc->info('signal.broadcast_ok', [
                'call_id' => $data['call_id'],
                'to' => $targetId,
                'type' => $data['type'],
            ]);
        } catch (\Throwable $e) {
            $rtc->warning('signal.broadcast_failed', [
                'type' => $data['type'],
                'call_id' => $data['call_id'],
                'error' => $e->getMessage(),
            ]);
        }

        // Web Push wake-up — only for offers (incoming call). Other signal
        // types (ice/answer/hangup) don't need OS-level notification since
        // by then the callee's browser is already engaged in the handshake.
        // Skipped for support offers because there's no single callee yet;
        // the in-browser toast on each admin's screen is sufficient.
        if ($data['type'] === 'offer' && !$isSupport && $targetId > 0) {
            try {
                $this->sendCallPush($targetId, $user, $data['call_id'], $data['call_type']);
            } catch (\Throwable $e) {
                $rtc->warning('signal.push_failed', [
                    'call_id' => $data['call_id'],
                    'to' => $targetId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Fire a Web Push to every push subscription registered for $targetId.
     * The service worker (public/push-sw.js) turns the payload into a
     * native OS notification with Answer / Decline actions — this is what
     * lets the callee's phone ring even when the browser tab is
     * backgrounded or the screen is off.
     *
     * Subscriptions that come back 404/410 are cleaned out — the push
     * service is telling us they're permanently invalid (user revoked
     * permission, uninstalled the browser, etc.).
     */
    protected function sendCallPush(int $targetId, User $caller, string $callId, string $callType): void
    {
        $publicKey  = (string) config('services.push.vapid_public', '');
        $privateKey = (string) config('services.push.vapid_private', '');
        if ($publicKey === '' || $privateKey === '') {
            // No VAPID keys — treat as not configured, silently skip.
            return;
        }

        $subs = \App\Models\PushSubscription::where('user_id', $targetId)->get();
        if ($subs->isEmpty()) return;

        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject'    => (string) config('services.push.vapid_subject', 'mailto:admin@evoory.com'),
                'publicKey'  => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);
        // Total budget per push call — most services respond in <500ms; a
        // 3s cap is plenty and keeps the signaling POST snappy for the caller.
        $webPush->setDefaultOptions(['TTL' => 45, 'urgency' => 'high']);

        $avatar = $caller->avatar ? asset('storage/' . $caller->avatar) : null;
        $payload = json_encode([
            'type'     => 'call.offer',
            'callId'   => $callId,
            'callType' => $callType,
            'from'     => [
                'id'     => $caller->id,
                'name'   => $caller->name ?: $caller->email ?: 'Someone',
                'avatar' => $avatar,
            ],
            'chatUrl'  => url('/my-chat'),
            'title'    => ($caller->name ?: 'Someone') . ' is calling',
            'body'     => $callType === 'video' ? 'Incoming video call' : 'Incoming voice call',
        ]);

        foreach ($subs as $sub) {
            $webPush->queueNotification(
                \Minishlink\WebPush\Subscription::create($sub->toWebPush()),
                $payload
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if ($report->isSuccess()) {
                \App\Models\PushSubscription::where('endpoint', $endpoint)
                    ->update(['last_used_at' => now()]);
                continue;
            }
            // 404/410 = subscription is permanently dead. Drop the row so
            // we don't keep hammering that endpoint on every call.
            $response = $report->getResponse();
            $status = $response ? $response->getStatusCode() : 0;
            if (in_array($status, [404, 410], true)) {
                \App\Models\PushSubscription::where('endpoint', $endpoint)->delete();
            }
        }
    }

    /**
     * Polling endpoint — returns any signals directed at the current user
     * that haven't been delivered yet, then marks them delivered so the
     * next poll doesn't re-emit them. Chat page hits this every ~1.5s.
     *
     * Also opportunistically prunes signals older than 5 minutes so the
     * queue doesn't grow unbounded.
     */
    public function pending(Request $request)
    {
        $t0 = microtime(true);
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'unauth'], 401);
        $rtc = Log::channel('rtc');

        // TEMP: log total controller time so we can distinguish controller
        // slowness from middleware slowness. If controller time is <50ms
        // but response takes seconds, the delay is in middleware (geo,
        // minify, session) or in the response write.
        register_shutdown_function(function () use ($t0) {
            $ms = (int) ((microtime(true) - $t0) * 1000);
            if ($ms > 100) {
                Log::warning("PENDING-CTRL took {$ms}ms");
            }
        });

        // PROD PERF (part 1): release the session file lock IMMEDIATELY.
        // Laravel's file session driver flock()s the session file for the
        // entire request lifetime, serializing all requests from the same
        // user. This endpoint polls every 500ms AND the chat page fires
        // /livewire/update on every click — without this save(), each poll
        // tick blocks the next chat click for its full duration, piling up
        // 1-2s of waiting per user action.
        if ($request->hasSession()) {
            $request->session()->save();
        }

        // PROD PERF (part 2): don't prune on every request. Table scan on
        // large call_signals tables was expensive. ~1% sample is plenty.
        if (mt_rand(1, 100) === 1) {
            try {
                CallSignalRow::where('created_at', '<', now()->subMinutes(5))->delete();
            } catch (\Throwable $e) {
                $rtc->warning('pending.prune_failed', ['error' => $e->getMessage()]);
            }
        }

        try {
            $rows = CallSignalRow::where('to_user_id', $user->id)
                ->whereNull('delivered_at')
                ->where('created_at', '>', now()->subMinute())
                ->orderBy('id')
                ->limit(50)
                ->get();
        } catch (\Throwable $e) {
            $rtc->error('pending.select_failed', [
                'auth_id' => $user->id,
                'error' => $e->getMessage(),
                'sql_state' => $e instanceof \PDOException ? ($e->errorInfo[0] ?? null) : null,
            ]);
            return response()->json([
                'error' => 'pending query failed',
                'detail' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        if ($rows->isNotEmpty()) {
            $rtc->info('pending.delivering', [
                'auth_id' => $user->id,
                'count' => $rows->count(),
                'types' => $rows->pluck('type')->all(),
                'call_ids' => $rows->pluck('call_id')->unique()->values()->all(),
            ]);
            CallSignalRow::whereIn('id', $rows->pluck('id'))
                ->update(['delivered_at' => now()]);
        }

        // Look up all distinct callers in one query so we can attach
        // fromName / fromAvatar to each signal payload for the callee's UI.
        $callerIds = $rows->pluck('from_user_id')->unique();
        $callers = User::whereIn('id', $callerIds)->get(['id', 'name', 'email', 'avatar'])->keyBy('id');

        return response()->json([
            'signals' => $rows->map(function ($r) use ($callers) {
                $u = $callers[$r->from_user_id] ?? null;
                return [
                    // Row id is used client-side as the dedup key, so a
                    // user sending the SAME (callId, type) twice during a
                    // call (e.g. two renegotiation offers for successive
                    // mid-call switches) isn't collapsed into one.
                    'id' => $r->id,
                    'callId' => $r->call_id,
                    'from' => $r->from_user_id,
                    'fromName' => $u?->name ?? $u?->email ?? 'Someone',
                    // Must match CallSignal::broadcastWith() — the raw
                    // asset('storage/'.$u->avatar) concat broke avatars
                    // for every user whose file wasn't under /storage.
                    // user_avatar_url() routes through user.avatar when
                    // an id is known and passes absolute URLs unchanged.
                    'fromAvatar' => $u ? user_avatar_url($u) : null,
                    'type' => $r->type,
                    'callType' => $r->call_type,
                    'payload' => $r->payload ?: [],
                ];
            })->values(),
        ]);
    }

    /**
     * Diagnostic — broadcasts a minimal test CallSignal to a target user
     * so we can verify Reverb delivery independently of SDP payload size.
     * Call from browser DevTools console:
     *   fetch('/rtc/ping-user/{TARGET_USER_ID}', {
     *     method:'POST',
     *     headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}
     *   })
     */
    /**
     * Health check — verifies each moving piece of the signaling stack and
     * writes a report to rtc.log so we can see exactly what's broken.
     *
     * GET /rtc/diag
     */
    public function diag(Request $request)
    {
        $rtc = Log::channel('rtc');
        $report = [
            'auth_id' => $request->user()?->id,
            'time' => now()->toIso8601String(),
            'checks' => [],
        ];

        // 1. Can we write to the rtc log at all?
        $rtc->info('diag.start', ['auth_id' => $report['auth_id']]);
        $report['checks']['log_write'] = 'ok';

        // 2. Does call_signals table exist and can we read it?
        try {
            $count = CallSignalRow::count();
            $report['checks']['call_signals_table'] = "ok ({$count} rows)";
        } catch (\Throwable $e) {
            $report['checks']['call_signals_table'] = 'FAIL: ' . $e->getMessage();
            $rtc->error('diag.call_signals_missing', ['error' => $e->getMessage()]);
        }

        // 3. Can we insert a test signal?
        try {
            $row = CallSignalRow::create([
                'call_id' => 'diag-' . uniqid(),
                'from_user_id' => $request->user()?->id ?? 0,
                'to_user_id' => $request->user()?->id ?? 0,
                'type' => 'ringing',
                'call_type' => 'audio',
                'payload' => ['diag' => true],
                'created_at' => now(),
            ]);
            $report['checks']['insert'] = 'ok (row #' . $row->id . ')';
            $row->delete();
        } catch (\Throwable $e) {
            $report['checks']['insert'] = 'FAIL: ' . $e->getMessage();
        }

        // 4. Can we reach Reverb HTTP API?
        try {
            broadcast(new CallSignal(
                $request->user()?->id ?? 0,
                $request->user()?->id ?? 0,
                'diag-' . uniqid(),
                'ringing',
                'audio',
                ['diag' => true]
            ));
            $report['checks']['broadcast'] = 'ok';
        } catch (\Throwable $e) {
            $report['checks']['broadcast'] = 'FAIL: ' . $e->getMessage();
            $rtc->error('diag.broadcast_failed', ['error' => $e->getMessage()]);
        }

        // 5. Config visibility
        $report['config'] = [
            'BROADCAST_DRIVER' => config('broadcasting.default'),
            'reverb.scheme' => config('broadcasting.connections.reverb.options.scheme'),
            'reverb.host' => config('broadcasting.connections.reverb.options.host'),
            'reverb.port' => config('broadcasting.connections.reverb.options.port'),
            'reverb.useTLS' => config('broadcasting.connections.reverb.options.useTLS'),
        ];

        $rtc->info('diag.done', $report);
        return response()->json($report);
    }

    public function pingUser(Request $request, int $targetUserId)
    {
        abort_unless($request->user(), 401);

        try {
            broadcast(new CallSignal(
                $targetUserId,
                $request->user()->id,
                'ping-' . uniqid(),
                'ringing',   // reuses ringing type — harmless if received
                'audio',
                ['ping' => true, 'ts' => now()->toISOString()]
            ))->toOthers();
            return response()->json(['ok' => true, 'target' => $targetUserId]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin claim-and-accept for a user→support call. Called by an admin
     * tapping Accept on the support-inbox toast.
     *
     * Race-safe via a conditional UPDATE: only the first admin whose UPDATE
     * matches (callee_id IS NULL + status='ringing') wins. Everyone else
     * sees 0 affected rows and the client dismisses their toast.
     *
     * On success, broadcasts a "claimed" signal on support-inbox so other
     * admins' toasts auto-dismiss (saves a few seconds of ghost ringing on
     * their end if Reverb delivery was slower than their Accept tap).
     * The accepting admin then runs the standard answer flow against the
     * caller via POST /call/signal with type=answer, which is what fills
     * in the audio/SDP path from this point forward.
     */
    public function supportAccept(Request $request)
    {
        $admin = $request->user();
        abort_unless($admin, 401);
        abort_unless($admin->is_admin ?? false, 403);

        $data = $request->validate([
            'call_id' => 'required|string|min:8|max:40',
        ]);

        $callId = $data['call_id'];

        // Atomic claim: UPDATE only fires if the row is still unclaimed and
        // still ringing. Returns the number of affected rows (0 or 1).
        $claimed = Call::where('call_id', $callId)
            ->where('is_support', true)
            ->whereNull('callee_id')
            ->where('status', 'ringing')
            ->update([
                'callee_id' => $admin->id,
            ]);

        if ($claimed !== 1) {
            // Someone else got there first (or the call is already over).
            return response()->json([
                'ok' => false,
                'reason' => 'already_claimed',
            ], 409);
        }

        $call = Call::where('call_id', $callId)->first();
        if (!$call) {
            return response()->json(['ok' => false, 'reason' => 'not_found'], 404);
        }

        // Fan out a "claimed" marker on support-inbox so every other admin's
        // toast dismisses. We also include the claimant's name so dismiss
        // UIs can say "Answered by X".
        try {
            broadcast(new CallSignal(
                0,                         // no specific target — support-inbox fanout
                $admin->id,
                $callId,
                'support-claimed',
                $call->type,
                [
                    'claimedBy' => $admin->id,
                    'claimedByName' => $admin->name ?? $admin->email,
                ],
                null,
                true                       // isSupport → broadcasts on support-inbox
            ))->toOthers();
        } catch (\Throwable $e) {
            // Non-fatal — other toasts will time out on their own.
            Log::channel('rtc')->warning('support-accept broadcast failed', [
                'call_id' => $callId,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'ok' => true,
            'call_id' => $callId,
            'caller_id' => $call->caller_id,
            // Caller name + avatar so the admin's UI can populate the
            // same incoming-call modal pieces (big avatar, name, etc.)
            // without a second lookup.
            'caller_name' => $call->caller->name ?? $call->caller->email ?? 'User',
            'caller_avatar' => $call->caller ? user_avatar_url($call->caller) : null,
            'call_type' => $call->type,
        ]);
    }

    /**
     * Recent call history for the current user (used by the "missed calls"
     * banner and the future call-log page).
     */
    public function history(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 401);

        $calls = Call::where(function ($q) use ($user) {
                $q->where('caller_id', $user->id)->orWhere('callee_id', $user->id);
            })
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($calls);
    }

    protected function recordLifecycle(
        string $callId,
        int $fromUserId,
        int $targetUserId,
        string $type,
        string $callType,
        ?int $conversationId,
        bool $isSupport = false
    ): void {
        // Offer creates the row; other signals update it.
        if ($type === 'offer') {
            // Support offers have no callee at create time — the first admin
            // to claim the call via /call/support-accept fills it in.
            Call::firstOrCreate(
                ['call_id' => $callId],
                [
                    'caller_id' => $fromUserId,
                    'callee_id' => $isSupport ? null : ($targetUserId ?: null),
                    'conversation_id' => $conversationId,
                    'type' => $callType,
                    'is_support' => $isSupport,
                    'status' => 'ringing',
                    'started_at' => now(),
                ]
            );
            return;
        }

        $call = Call::where('call_id', $callId)->first();
        if (!$call) return;

        switch ($type) {
            case 'answer':
                if ($call->status === 'ringing') {
                    $call->status = 'answered';
                    $call->connected_at = now();
                    $call->save();
                }
                break;

            case 'decline':
                if ($call->status === 'ringing') {
                    $call->status = 'declined';
                    $call->ended_at = now();
                    $call->save();
                    $this->emitCallSummaryMessage($call);
                }
                break;

            case 'hangup':
                if ($call->status === 'ringing') {
                    $call->status = $fromUserId === $call->caller_id ? 'missed' : 'declined';
                } elseif ($call->status === 'answered') {
                    $call->status = 'ended';
                }
                $call->ended_at = now();
                if ($call->connected_at) {
                    $call->duration = Carbon::parse($call->connected_at)->diffInSeconds(now());
                }
                $call->save();
                $this->emitCallSummaryMessage($call);
                break;
        }
    }

    /**
     * Mint short-lived ICE server credentials for the WebRTC client using the
     * REST/HMAC scheme (RFC 7635 / draft-uberti-behave-turn-rest). The coturn
     * server has `use-auth-secret` + `static-auth-secret=<shared>` set, so the
     * credential the browser presents doesn't have to be pre-registered — coturn
     * validates it on the fly by re-computing the HMAC. This lets us hand out
     * per-user, time-limited creds without any allocation on the TURN server.
     *
     * Username: "<expiry-unix-ts>:<user-id>"
     * Credential: base64(hmac_sha1(username, shared_secret))
     *
     * We serve UDP + TCP + TLS variants so clients on restrictive networks
     * (Opera's built-in VPN, corporate proxies, hotel wifi, some cellular)
     * that block UDP:3478 outbound can fall back to TCP:3478 or TLS:5349
     * (which looks like regular HTTPS to firewalls). Confirmed necessary
     * when Opera on a friend's network gathered ZERO relay candidates with
     * UDP-only — its `onicecandidate` never fired between gathering and
     * complete.
     *
     * The earlier concern about asymmetric ICE pairs across browsers was
     * actually caused by the SDP private-IP leak (see
     * project_webrtc_private_ip_sdp_leak.md), not by multiple transports.
     * Now that sanitizeSdp() handles both incoming and outgoing SDP, serving
     * multiple transports is safe again and materially improves reachability.
     */
    public function turnCredentials(Request $request)
    {
        abort_unless($request->user(), 401);

        $host   = (string) config('services.turn.host', '');
        $secret = (string) config('services.turn.secret', '');
        $ttl    = (int) config('services.turn.ttl', 86400);
        if ($ttl < 300 || $ttl > 86400) $ttl = 86400;

        $iceServers = [];
        $sources    = [];

        // Cloudflare Realtime TURN (primary when configured). Browsers try
        // this entry first; if it fails they fall back to coturn below.
        $cfTokenId = (string) config('services.cloudflare_turn.token_id', '');
        $cfApiToken = (string) config('services.cloudflare_turn.api_token', '');
        if ($cfTokenId !== '' && $cfApiToken !== '') {
            $cfIce = $this->mintCloudflareIceServers($cfTokenId, $cfApiToken, (int) config('services.cloudflare_turn.ttl', 86400));
            if ($cfIce) {
                $iceServers = array_merge($iceServers, $cfIce);
                $sources[]  = 'cloudflare';
            }
        }

        // Coturn fallback via REST/HMAC scheme.
        if ($host !== '' && $secret !== '') {
            $username   = (time() + $ttl) . ':' . $request->user()->id;
            $credential = base64_encode(hash_hmac('sha1', $username, $secret, true));
            $iceServers[] = ['urls' => "stun:{$host}:3478"];
            $iceServers[] = ['urls' => "turn:{$host}:3478?transport=udp",  'username' => $username, 'credential' => $credential];
            $iceServers[] = ['urls' => "turn:{$host}:3478?transport=tcp",  'username' => $username, 'credential' => $credential];
            $iceServers[] = ['urls' => "turns:{$host}:5349?transport=tcp", 'username' => $username, 'credential' => $credential];
            $sources[]    = 'coturn-hmac';
        }

        // Last-resort: public STUN only. Cross-symmetric-NAT will fail cleanly
        // rather than hang. Used only when nothing else is configured.
        if (empty($iceServers)) {
            $iceServers[] = ['urls' => 'stun:stun.l.google.com:19302'];
            $sources[]    = 'stun-only';
        }

        return response()->json([
            'iceServers' => $iceServers,
            'source'     => implode('+', $sources),
        ])->header('Cache-Control', 'no-store, private, max-age=0, must-revalidate');
    }

    /**
     * POST to Cloudflare Realtime TURN to mint short-lived credentials.
     * Cloudflare returns iceServers with TWO entries: a STUN-only entry
     * and a TURN entry bundling 6 URL variants (UDP/TCP/TLS on 3478/5349
     * plus port 80/443 fallbacks for restrictive networks). Both are
     * returned so the browser can use whichever reaches. Returns null on
     * failure so the coturn/STUN fallback still works.
     */
    protected function mintCloudflareIceServers(string $tokenId, string $apiToken, int $ttl): ?array
    {
        if ($ttl < 300 || $ttl > 86400) $ttl = 86400;

        try {
            $url = "https://rtc.live.cloudflare.com/v1/turn/keys/{$tokenId}/credentials/generate-ice-servers";
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 4,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $apiToken,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode(['ttl' => $ttl]),
            ]);
            $body   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status < 200 || $status >= 300 || !$body) return null;
            $data = json_decode($body, true);
            if (!is_array($data) || empty($data['iceServers']) || !is_array($data['iceServers'])) return null;
            return $data['iceServers'];
        } catch (\Throwable $e) {
            \Log::warning('Cloudflare TURN credential fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create an ephemeral Daily.co room for a 1:1 call. Rooms auto-expire
     * after 2 hours so orphaned URLs can't be replayed. The caller hits
     * this endpoint at call-start time, then broadcasts the room URL to
     * the callee via the existing /call/signal channel; both peers open
     * the same Daily.co iframe and Daily handles all media relaying.
     */
    public function createRoom(Request $request)
    {
        abort_unless($request->user(), 401);
        $rtc = Log::channel('rtc');

        $apiKey = (string) config('services.daily.api_key', '');
        if ($apiKey === '') {
            $rtc->error('daily.not_configured');
            return response()->json(['error' => 'Daily.co not configured'], 500);
        }

        $isVideo = $request->input('type') === 'video';

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(6)
                ->post('https://api.daily.co/v1/rooms', [
                    'privacy' => 'private',
                    'properties' => [
                        'exp'                => time() + 7200, // 2h max lifespan
                        'max_participants'   => 2,
                        'enable_prejoin_ui'  => false,
                        'enable_screenshare' => $isVideo,
                        'enable_chat'        => false,
                        'start_video_off'    => !$isVideo,
                        'start_audio_off'    => false,
                    ],
                ]);

            if (!$response->successful()) {
                $rtc->error('daily.create_room_failed', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 300),
                ]);
                return response()->json(['error' => 'Failed to create room'], 500);
            }

            $room = $response->json();

            // Mint a meeting token so the user joins as themselves (avatar/name)
            // and can't rejoin after the room expires.
            $tokenResp = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(6)
                ->post('https://api.daily.co/v1/meeting-tokens', [
                    'properties' => [
                        'room_name'   => $room['name'],
                        'user_name'   => $request->user()->name ?: ('User ' . $request->user()->id),
                        'user_id'     => (string) $request->user()->id,
                        'exp'         => time() + 7200,
                        'enable_screenshare' => $isVideo,
                    ],
                ]);

            $token = $tokenResp->successful() ? ($tokenResp->json()['token'] ?? null) : null;

            return response()->json([
                'roomUrl'  => $room['url'],
                'roomName' => $room['name'],
                'token'    => $token,
            ]);
        } catch (\Throwable $e) {
            $rtc->error('daily.exception', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Daily.co API error'], 500);
        }
    }

    /**
     * Mint a meeting token for a CALLEE joining an existing Daily.co room.
     * The caller already has a token from createRoom(); this lets the
     * callee join the same room with their own identity.
     */
    public function joinRoom(Request $request)
    {
        abort_unless($request->user(), 401);
        $data = $request->validate([
            'room_name' => 'required|string|max:80',
        ]);

        $apiKey = (string) config('services.daily.api_key', '');
        if ($apiKey === '') {
            return response()->json(['error' => 'Daily.co not configured'], 500);
        }

        try {
            $tokenResp = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(6)
                ->post('https://api.daily.co/v1/meeting-tokens', [
                    'properties' => [
                        'room_name' => $data['room_name'],
                        'user_name' => $request->user()->name ?: ('User ' . $request->user()->id),
                        'user_id'   => (string) $request->user()->id,
                        'exp'       => time() + 7200,
                    ],
                ]);

            if (!$tokenResp->successful()) {
                return response()->json(['error' => 'Token mint failed'], 500);
            }

            return response()->json(['token' => $tokenResp->json()['token'] ?? null]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Daily.co API error'], 500);
        }
    }

    /**
     * Insert a WhatsApp-style call-summary message into the associated
     * conversation so both participants see the call outcome in their thread.
     * Reuses the existing attachment_* columns (no schema change):
     *
     *   attachment_type     = 'call'
     *   attachment_mime     = 'audio' | 'video'         (the call kind)
     *   attachment_original_name = final Call.status    (missed|declined|ended)
     *   attachment_duration = seconds when status=ended (nullable otherwise)
     *
     * The chat view renders this as a call bubble with a phone icon,
     * direction arrow, status text and (if answered) duration.
     */
    protected function emitCallSummaryMessage(Call $call): void
    {
        if (!$call->conversation_id) {
            // Missing — try to look up the 1:1 conversation between the pair.
            $conv = Conversation::forUser($call->caller_id)
                ->where(function ($q) use ($call) {
                    $q->where('user_one_id', $call->callee_id)
                      ->orWhere('user_two_id', $call->callee_id);
                })
                ->where('is_support', false)
                ->first();
            if (!$conv) return;
            $call->conversation_id = $conv->id;
            $call->save();
        }

        try {
            $message = Message::create([
                'conversation_id' => $call->conversation_id,
                'sender_id' => $call->caller_id,
                'message' => '',
                'status' => 'sent',
                'attachment_type' => 'call',
                'attachment_mime' => $call->type,              // 'audio' | 'video'
                'attachment_original_name' => $call->status,   // 'missed' | 'declined' | 'ended'
                'attachment_duration' => $call->duration,
            ]);

            Conversation::where('id', $call->conversation_id)->update(['last_message_at' => now()]);

            // Broadcast to the callee so the message appears in real time.
            try {
                broadcast(new NewChatMessage($message, $call->callee_id))->toOthers();
            } catch (\Throwable $e) {
                Log::channel('rtc')->warning('call_summary.broadcast_failed', [
                    'call_id' => $call->call_id,
                    'error' => $e->getMessage(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('rtc')->error('call_summary.insert_failed', [
                'call_id' => $call->call_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
