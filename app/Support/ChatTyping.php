<?php

namespace App\Support;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * WhatsApp-style "typing…" state for /my-chat and the admin Support Inbox.
 *
 * Neither page can count on the Reverb socket (customer pages don't load
 * Echo, and the inbox falls back to polling when it drops), so typing goes
 * over HTTP: the typist pings /chat/typing/{conversation} while typing and
 * the other side polls it. Each ping keeps a short cache entry alive, so the
 * indicator lapses by itself shortly after the typing stops.
 *
 * A chat has two sides. In a support chat they are the customer (user_one)
 * and "support" — any admin — so the customer sees whichever agent types.
 */
class ChatTyping
{
    // Seconds a ping keeps "typing" alive; the client pings every 2 s.
    public const TTL = 5;

    /** The side $user types as in $conversation, or null if they're not in it. */
    public static function sideOf(Conversation $conversation, User $user): ?string
    {
        if ($conversation->is_support) {
            if ((int) $conversation->user_one_id === (int) $user->id) return 'u' . $user->id;
            return ($user->is_admin ?? false) ? 'support' : null;
        }

        return in_array((int) $user->id, [(int) $conversation->user_one_id, (int) $conversation->user_two_id], true)
            ? 'u' . $user->id
            : null;
    }

    /** The side $viewer watches: the other party. */
    public static function peerSideOf(Conversation $conversation, User $viewer): ?string
    {
        $side = static::sideOf($conversation, $viewer);
        if ($side === null) return null;

        if ($conversation->is_support) {
            return $side === 'support' ? 'u' . $conversation->user_one_id : 'support';
        }

        $otherId = (int) $conversation->user_one_id === (int) $viewer->id
            ? (int) $conversation->user_two_id
            : (int) $conversation->user_one_id;

        return $otherId ? 'u' . $otherId : null;
    }

    public static function start(int $conversationId, string $side): void
    {
        Cache::put(static::key($conversationId, $side), 1, static::TTL);
    }

    public static function stop(int $conversationId, string $side): void
    {
        Cache::forget(static::key($conversationId, $side));
    }

    /** Whether the other party is typing to $viewer right now. */
    public static function isPeerTyping(Conversation $conversation, User $viewer): bool
    {
        $peerSide = static::peerSideOf($conversation, $viewer);
        if ($peerSide === null || !Cache::has(static::key($conversation->id, $peerSide))) return false;

        // Blocked either way: no typing, as no messages get through either.
        if (!$conversation->is_support) {
            $otherId = (int) substr($peerSide, 1);
            $blocked = DB::table('user_blocks')
                ->where(fn ($q) => $q->where('blocker_id', $viewer->id)->where('blocked_id', $otherId))
                ->orWhere(fn ($q) => $q->where('blocker_id', $otherId)->where('blocked_id', $viewer->id))
                ->exists();
            if ($blocked) return false;
        }

        return true;
    }

    /**
     * A sent message ends its sender's typing at once, whichever way it was
     * sent (text, voice note, upload, admin reply).
     */
    public static function clearForMessage(Message $message, Conversation $conversation): void
    {
        $side = $conversation->is_support && (int) $message->sender_id !== (int) $conversation->user_one_id
            ? 'support'
            : 'u' . $message->sender_id;

        static::stop((int) $conversation->id, $side);
    }

    protected static function key(int $conversationId, string $side): string
    {
        return "chat-typing:{$conversationId}:{$side}";
    }
}
