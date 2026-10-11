<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Per-user chat badge counts (unread messages + unseen missed calls),
 * shared across the mobile bottom nav, the desktop header, and the
 * communication nav. Each of those components previously ran its own
 * copy of the same query on every page load — 2-3 duplicate queries
 * per request on EVERY authenticated page, which is what tanked the
 * home / listings pages after chat shipped.
 *
 * This helper collapses them into one JOIN query (replacing the
 * Eloquent whereHas correlated subquery) + a short Cache::remember
 * window so repeat page loads skip the DB entirely.
 */
class ChatBadges
{
    protected static array $requestCache = [];

    /**
     * @return array{unread_messages: int, missed_calls: int, total: int}
     */
    public static function for(?int $userId): array
    {
        if (!$userId) {
            return ['unread_messages' => 0, 'missed_calls' => 0, 'total' => 0];
        }

        if (isset(self::$requestCache[$userId])) {
            return self::$requestCache[$userId];
        }

        $result = Cache::remember("chat.badges.{$userId}", 30, function () use ($userId) {
            // Two separate indexed lookups then UNION — lets MySQL use
            // conversations(user_one_id, ...) AND conversations(user_two_id, ...)
            // cleanly. A single `WHERE user_one_id = X OR user_two_id = X`
            // on the same table forces an index-merge-or-full-scan plan
            // that the planner often got wrong on prod.
            $convIds = DB::table('conversations')->where('user_one_id', $userId)->pluck('id')
                ->merge(DB::table('conversations')->where('user_two_id', $userId)->pluck('id'))
                ->unique()
                ->values();

            if ($convIds->isEmpty()) {
                $unread = 0;
            } else {
                $unread = (int) DB::table('messages')
                    ->whereIn('conversation_id', $convIds)
                    ->where('sender_id', '!=', $userId)
                    ->where(function ($q) {
                        $q->whereNull('status')
                          ->orWhereIn('status', ['sent', 'delivered', 'unread']);
                    })
                    ->count();
            }

            $missed = (int) DB::table('calls')
                ->where('callee_id', $userId)
                ->where('status', 'missed')
                ->whereNull('seen_at')
                ->count();

            return [
                'unread_messages' => $unread,
                'missed_calls'    => $missed,
                'total'           => $unread + $missed,
            ];
        });

        self::$requestCache[$userId] = $result;
        return $result;
    }

    /**
     * Call this whenever we flip chat state that affects a user's badge
     * (new message, mark-as-read, missed-call-seen). The 30s TTL on top
     * of this is just a safety net for places we forget to invalidate.
     */
    public static function clear(?int $userId): void
    {
        if (!$userId) return;
        unset(self::$requestCache[$userId]);
        Cache::forget("chat.badges.{$userId}");
    }
}
