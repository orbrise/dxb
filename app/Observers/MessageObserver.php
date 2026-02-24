<?php

namespace App\Observers;

use App\Events\NewMessageReceived;
use App\Events\MessageSent;
use App\Models\Message;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        try {
            // Load the profile relationship
            $message->load('profile');
            
            if (!$message->profile) {
                return;
            }
            
            // Get the user ID who owns this profile
            $userId = $message->profile->user_id;
            
            if ($userId) {
                // Determine if this is an incoming message or a reply
                if ($message->status === 'sent') {
                    // This is our reply - broadcast to conversation channel
                    broadcast(new MessageSent($message, $message->user_email))->toOthers();
                } else {
                    // This is an incoming message from a visitor
                    broadcast(new NewMessageReceived($message, $userId))->toOthers();
                }
                
                Log::debug('Message broadcast sent', [
                    'message_id' => $message->id,
                    'user_id' => $userId,
                    'status' => $message->status,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error broadcasting message: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        // Broadcast when a message gets a reply
        if ($message->isDirty('reply') && $message->reply) {
            try {
                $message->load('profile');
                
                if ($message->profile && $message->profile->user_id) {
                    broadcast(new MessageSent($message, $message->user_email))->toOthers();
                }
            } catch (\Exception $e) {
                Log::error('Error broadcasting message update: ' . $e->getMessage());
            }
        }
    }
}
