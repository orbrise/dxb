<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel for receiving new messages (user-specific)
Broadcast::channel('messages.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Channel for specific conversation (user + sender email hash)
Broadcast::channel('conversation.{userId}.{emailHash}', function ($user, $userId, $emailHash) {
    return (int) $user->id === (int) $userId;
});

// Private channel for real-time chat - users can only listen to their own channel
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Shared support-inbox channel — any admin can subscribe. Used to fan out
// customer-support messages to every admin's inbox in real time.
Broadcast::channel('support-inbox', function ($user) {
    return (bool) ($user->is_admin ?? false);
});
