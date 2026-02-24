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

class MessageSent implements ShouldBroadcastNow
{ 
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public string $senderEmail;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, string $senderEmail)
    {
        $this->message = $message;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast to the sender's channel so they see their own messages
        return [
            new PrivateChannel('conversation.' . $this->message->profile->user_id . '.' . md5($this->senderEmail)),
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
        return 'message.sent';
    }
}
