<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Per-user marker that changes whenever a message in one of that user's
 * chats is created, updated or deleted. /my-chat has no Reverb socket, so it
 * checks this every second (GET /chat/rev reads only this cache entry) and
 * runs its full refreshChat only when it moved. A reply then shows in about a
 * second without refreshing the whole chat every second. The admin Support
 * Inbox has its own marker (SupportInboxRevision).
 */
class ChatRevision
{
    public static function bump(?int ...$userIds): void
    {
        $stamp = (string) hrtime(true);
        foreach (array_unique(array_filter($userIds)) as $userId) {
            Cache::forever(static::key($userId), $stamp);
        }
    }

    public static function current(int $userId): string
    {
        return (string) Cache::get(static::key($userId), '0');
    }

    protected static function key(int $userId): string
    {
        return "chat-rev:{$userId}";
    }
}
