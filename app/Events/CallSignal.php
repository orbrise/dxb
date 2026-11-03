<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Generic WebRTC signaling event. Payload is opaque to the server — it just
 * routes SDP offers/answers, ICE candidates, hangup/decline/ringing signals
 * from one peer's client-side to the other's over Reverb.
 */
class CallSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $targetUserId;   // 0 for support-fanout (no specific callee yet)
    public int $fromUserId;
    public string $callId;
    public string $type;        // offer | answer | ice | hangup | decline | ringing
    public string $callType;    // audio | video (only meaningful on offer/ringing)
    public array $payload;      // SDP / ICE candidate / meta
    public ?int $signalId;      // call_signals row id (dedup key client-side)
    public bool $isSupport;     // fan-out on support-inbox too

    public function __construct(
        int $targetUserId,
        int $fromUserId,
        string $callId,
        string $type,
        string $callType,
        array $payload = [],
        ?int $signalId = null,
        bool $isSupport = false
    ) {
        $this->targetUserId = $targetUserId;
        $this->fromUserId   = $fromUserId;
        $this->callId       = $callId;
        $this->type         = $type;
        $this->callType     = $callType;
        $this->payload      = $payload;
        $this->signalId     = $signalId;
        $this->isSupport    = $isSupport;
    }

    public function broadcastOn(): array
    {
        $channels = [];
        // targetUserId 0 is the "no specific callee" case used by the
        // initial support offer — fan it out exclusively on the shared
        // support-inbox so every admin's listener picks it up.
        if ($this->targetUserId > 0) {
            $channels[] = new PrivateChannel('chat.' . $this->targetUserId);
        }
        if ($this->isSupport) {
            $channels[] = new PrivateChannel('support-inbox');
        }
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'CallSignal';
    }

    public function broadcastWith(): array
    {
        // Include the caller's display name + avatar so the callee's
        // incoming-call banner shows "John" instead of the fallback "Someone".
        // Cached on the event instance so repeat calls (broadcast + polling)
        // hit the DB at most once per event.
        //
        // Avatar MUST go through user_avatar_url(). Previously we did
        // `asset('storage/' . $u->avatar)` which broke for every user
        // whose avatar wasn't stored under /storage — absolute URLs got
        // double-prefixed, nested paths (avatars/, profile-pics/, etc.)
        // 404'd, and the incoming-call banner fell back to the lime
        // initial-letter chip even though the sidebar showed the real
        // image fine (because the sidebar uses this same helper).
        if (!isset($this->_fromMeta)) {
            $u = \App\Models\User::find($this->fromUserId);
            $this->_fromMeta = [
                'fromName'   => $u?->name ?? $u?->email ?? 'Someone',
                'fromAvatar' => $u ? user_avatar_url($u) : null,
            ];
        }

        return [
            'id' => $this->signalId,
            'from' => $this->fromUserId,
            'fromName' => $this->_fromMeta['fromName'],
            'fromAvatar' => $this->_fromMeta['fromAvatar'],
            'callId' => $this->callId,
            'type' => $this->type,
            'callType' => $this->callType,
            'payload' => $this->payload,
            'isSupport' => $this->isSupport,
        ];
    }

    private ?array $_fromMeta = null;
}
