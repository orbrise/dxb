<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * A marker that changes whenever a support-chat message is created, updated
 * or deleted. The admin Support Inbox checks it every second while its Reverb
 * socket is down (GET /admin/support-inbox/rev reads only this cache entry)
 * and runs its full Livewire refresh only when it moved, so a customer's
 * message shows in about a second instead of waiting for the slow poll.
 */
class SupportInboxRevision
{
    protected const KEY = 'support-inbox:rev';

    public static function bump(): void
    {
        Cache::forever(static::KEY, (string) hrtime(true));
    }

    /**
     * For bulk status updates (delivered / read receipts), which skip the
     * Message model events: bump only if one of these chats is a support chat.
     */
    public static function bumpIfSupport(iterable $conversationIds): void
    {
        $ids = collect($conversationIds)->map(fn ($id) => (int) $id)->filter()->unique()->values();
        if ($ids->isEmpty()) return;

        try {
            if (\App\Models\Conversation::whereIn('id', $ids)->where('is_support', true)->exists()) {
                static::bump();
            }
        } catch (\Throwable $e) {
            // Non-critical — the inbox's slower safety-net poll still catches it.
        }
    }

    public static function current(): string
    {
        return (string) Cache::get(static::KEY, '0');
    }
}
