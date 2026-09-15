<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public int $receiverId;

    /**
     * @param  Message  $message
     * @param  int      $receiverId  0 = broadcast to shared support-inbox channel
     *                               (any admin can subscribe); otherwise the
     *                               specific recipient's private chat channel.
     */
    public function __construct(Message $message, int $receiverId)
    {
        $this->message = $message;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn(): array
    {
        // Support messages: only to the shared support-inbox channel.
        // Non-support messages: to the recipient's private chat channel.
        // Admin replies to a support convo: to BOTH the customer's chat channel
        // AND the support-inbox (so other admins see the update in real time).
        $channels = [];

        $conversation = $this->message->conversation;
        $isSupport = $conversation && $conversation->is_support;

        if ($isSupport) {
            $channels[] = new PrivateChannel('support-inbox');

            // If admin is replying, also push to the customer's channel.
            if ($this->receiverId > 0) {
                $channels[] = new PrivateChannel('chat.' . $this->receiverId);
            }
        } else {
            if ($this->receiverId > 0) {
                $channels[] = new PrivateChannel('chat.' . $this->receiverId);
            }
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender?->name ?? $this->message->sender?->email ?? 'Unknown',
                'message' => $this->message->message,
                'status' => $this->message->status,
                'created_at' => $this->message->created_at->toISOString(),
                'is_support' => (bool) ($this->message->conversation?->is_support ?? false),
                'is_mine' => false,
                'attachment_url' => $this->message->attachment_url,
                'attachment_type' => $this->message->attachment_type,
                'attachment_mime' => $this->message->attachment_mime,
                'attachment_size' => $this->message->attachment_size,
                'attachment_duration' => $this->message->attachment_duration,
                'attachment_original_name' => $this->message->attachment_original_name,
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewChatMessage';
    }
}
