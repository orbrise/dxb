<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $conversationId;
    public string $newStatus;
    public int $senderId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $conversationId, string $newStatus, int $senderId)
    {
        $this->conversationId = $conversationId;
        $this->newStatus = $newStatus;
        $this->senderId = $senderId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Broadcast to the original sender so they can update tick marks
        return [
            new PrivateChannel('chat.' . $this->senderId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MessageStatusUpdated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'status' => $this->newStatus,
        ];
    }
}
