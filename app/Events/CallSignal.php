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

    public int $targetUserId;
    public int $fromUserId;
    public string $callId;
    public string $type;        // offer | answer | ice | hangup | decline | ringing
    public string $callType;    // audio | video (only meaningful on offer/ringing)
    public array $payload;      // SDP / ICE candidate / meta

    public function __construct(
        int $targetUserId,
        int $fromUserId,
        string $callId,
        string $type,
        string $callType,
        array $payload = []
    ) {
        $this->targetUserId = $targetUserId;
        $this->fromUserId   = $fromUserId;
        $this->callId       = $callId;
        $this->type         = $type;
        $this->callType     = $callType;
        $this->payload      = $payload;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.' . $this->targetUserId)];
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
        if (!isset($this->_fromMeta)) {
            $u = \App\Models\User::find($this->fromUserId);
            $this->_fromMeta = [
                'fromName'   => $u?->name ?? $u?->email ?? 'Someone',
                'fromAvatar' => $u?->avatar ? asset('storage/' . $u->avatar) : null,
            ];
        }

        return [
            'from' => $this->fromUserId,
            'fromName' => $this->_fromMeta['fromName'],
            'fromAvatar' => $this->_fromMeta['fromAvatar'],
            'callId' => $this->callId,
            'type' => $this->type,
            'callType' => $this->callType,
            'payload' => $this->payload,
        ];
    }

    private ?array $_fromMeta = null;
}
