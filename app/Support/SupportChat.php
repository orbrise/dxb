<?php

namespace App\Support;

use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

/**
 * Sends a message from a user into their Support chat from outside /my-chat
 * (e.g. Buy Credits → Contact Support). Same row + broadcast as Chat's own
 * Send; Message's created hooks bump the chat / Support Inbox revisions.
 */
class SupportChat
{
    public static function send(int $userId, string $text): Message
    {
        $conversation = Conversation::getOrCreateSupport($userId);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'message' => $text,
            'status' => 'sent',
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            broadcast(new NewChatMessage($message, 0))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return $message;
    }
}
