<?php

namespace App\Http\Controllers;

use App\Events\CallSignal;
use App\Events\NewChatMessage;
use App\Models\Call;
use App\Models\CallSignal as CallSignalRow;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
                // Note: don't use Laravel's `different:` rule here — that
                // compares two REQUEST fields, not against a value. The
                // self-call check is done manually below.
                'target_user_id' => 'required|integer|exists:users,id',
                'call_id'        => 'required|string|min:8|max:40',
                'type'           => 'required|string|in:offer,answer,ice,hangup,decline,ringing',
                'call_type'      => 'required|string|in:audio,video',
                'payload'        => 'nullable|array',
                'conversation_id'=> 'nullable|integer|exists:conversations,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            $rtc->warning('signal.validation_failed', [
                'errors' => $ve->errors(),
                'input' => $request->except(['payload']),
            ]);
            throw $ve;
        }

        $targetId = (int) $data['target_user_id'];
        if ($targetId === $user->id) {
            $rtc->warning('signal.self_call_rejected', ['auth_id' => $user->id]);
            return response()->json(['error' => 'cannot call yourself'], 422);
        }

        try {
            $this->recordLifecycle(
                $data['call_id'],
                $user->id,
                $targetId,
                $data['type'],
                $data['call_type'],
                $data['conversation_id'] ?? null
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
        // polling path picks it up a moment later.
        try {
            broadcast(new CallSignal(
                $targetId,
                $user->id,
                $data['call_id'],
                $data['type'],
                $data['call_type'],
                $data['payload'] ?? []
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

        return response()->json(['ok' => true]);
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
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'unauth'], 401);
        $rtc = Log::channel('rtc');

        try {
            CallSignalRow::where('created_at', '<', now()->subMinutes(5))->delete();
        } catch (\Throwable $e) {
            $rtc->warning('pending.prune_failed', ['error' => $e->getMessage()]);
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
                    'callId' => $r->call_id,
                    'from' => $r->from_user_id,
                    'fromName' => $u?->name ?? $u?->email ?? 'Someone',
                    'fromAvatar' => $u?->avatar ? asset('storage/' . $u->avatar) : null,
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
        ?int $conversationId
    ): void {
        // Offer creates the row; other signals update it.
        if ($type === 'offer') {
            Call::firstOrCreate(
                ['call_id' => $callId],
                [
                    'caller_id' => $fromUserId,
                    'callee_id' => $targetUserId,
                    'conversation_id' => $conversationId,
                    'type' => $callType,
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
     * We serve UDP + TCP + TLS variants of the same TURN URL so the client
     * picks whichever gets through its firewall. UDP is fastest; TCP/TLS are
     * fallbacks for restrictive networks (corporate proxies, some CGNAT).
     */
    public function turnCredentials(Request $request)
    {
        abort_unless($request->user(), 401);

        $host   = (string) config('services.turn.host', '');
        $secret = (string) config('services.turn.secret', '');
        $ttl    = (int) config('services.turn.ttl', 86400);
        if ($ttl < 300 || $ttl > 86400) $ttl = 86400;

        // If TURN isn't configured (local dev), return STUN-only so calls at
        // least attempt over public IPs — cross-symmetric-NAT will fail
        // cleanly rather than hang.
        if ($host === '' || $secret === '') {
            return response()->json([
                'iceServers' => [
                    ['urls' => 'stun:stun.l.google.com:19302'],
                ],
                'source' => 'stun-only',
            ])->header('Cache-Control', 'no-store, private, max-age=0, must-revalidate');
        }

        $username   = (time() + $ttl) . ':' . $request->user()->id;
        $credential = base64_encode(hash_hmac('sha1', $username, $secret, true));

        return response()->json([
            'iceServers' => [
                ['urls' => [
                    'stun:stun.l.google.com:19302',
                    'stun:stun1.l.google.com:19302',
                    "stun:{$host}:3478",
                ]],
                ['urls' => "turn:{$host}:3478?transport=udp",  'username' => $username, 'credential' => $credential],
                ['urls' => "turn:{$host}:3478?transport=tcp",  'username' => $username, 'credential' => $credential],
                ['urls' => "turns:{$host}:5349?transport=tcp", 'username' => $username, 'credential' => $credential],
            ],
            'source' => 'coturn-hmac',
        ])->header('Cache-Control', 'no-store, private, max-age=0, must-revalidate');
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
