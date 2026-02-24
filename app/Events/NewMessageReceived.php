<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageReceived implements ShouldBroadcastNow
{ 
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, int $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messages.' . $this->userId),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'user_email' => $this->message->user_email,
                'profile_id' => $this->message->profile_id,
                'message' => $this->message->message,
                'code' => $this->message->code,
                'phone' => $this->message->phone,
                'status' => $this->message->status,
                'reply' => $this->message->reply,
                'created_at' => $this->message->created_at->toISOString(),
                'profile' => $this->message->profile ? [
                    'id' => $this->message->profile->id,
                    'name' => $this->message->profile->name,
                ] : null,
            ],
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.received';
    }
}
