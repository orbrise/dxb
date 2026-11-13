<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * The free plan's limit on who a user can talk to: without an active
 * Premium plan, a user can message and call FREE_PEOPLE people. Everyone
 * they message or call counts, replies included, staff accounts too; only the
 * Support chat doesn't. (Staff themselves are never limited.) People they've
 * already talked to stay allowed, so nobody loses
 * a chat they have; only a new person is refused (the client then shows the
 * upgrade popup, partials/_premium-limit-modal).
 *
 * Who they've talked to is kept in chat_contacts rather than read from
 * messages: deleting a chat removes its messages, which would otherwise free
 * up a slot.
 */
class ChatAllowance
{
    public const FREE_PEOPLE = 2;

    /** The error code endpoints return, which the client turns into the popup. */
    public const ERROR = 'premium_required';

    public static function isLimited(User $user): bool
    {
        return !$user->is_admin && !$user->hasActivePremium();
    }

    /** How many people $user has talked to. Deleted accounts don't count. */
    public static function used(User $user): int
    {
        try {
            return DB::table('chat_contacts')
                ->join('users', 'users.id', '=', 'chat_contacts.peer_id')
                ->where('chat_contacts.user_id', $user->id)
                ->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public static function remaining(User $user): int
    {
        return max(0, self::FREE_PEOPLE - self::used($user));
    }

    /**
     * Whether $user may message or call $peerId. Fails open: an error here
     * (e.g. chat_contacts not migrated yet) must not stop people chatting.
     */
    public static function allows(User $user, int $peerId): bool
    {
        if (!self::isLimited($user) || $peerId <= 0 || $peerId === (int) $user->id) return true;

        try {
            $known = DB::table('chat_contacts')->where('user_id', $user->id)->where('peer_id', $peerId)->exists();
            if ($known) return true;

            return self::used($user) < self::FREE_PEOPLE;
        } catch (\Throwable $e) {
            Log::warning('ChatAllowance: check failed, allowing', ['user_id' => $user->id, 'peer_id' => $peerId, 'error' => $e->getMessage()]);
            return true;
        }
    }

    /** Remember that $userId talked to $peerId. Called for every message and call. */
    public static function record(int $userId, int $peerId): void
    {
        if ($userId <= 0 || $peerId <= 0 || $userId === $peerId) return;

        try {
            DB::table('chat_contacts')->insertOrIgnore([
                'user_id' => $userId,
                'peer_id' => $peerId,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Never fail a send or call over the count (e.g. not migrated yet).
            Log::warning('ChatAllowance: record failed', ['user_id' => $userId, 'peer_id' => $peerId, 'error' => $e->getMessage()]);
        }
    }

    /** JSON body for a refused send or call. */
    public static function errorBody(): array
    {
        return [
            'error' => self::ERROR,
            'detail' => 'Your free plan lets you message and call ' . self::FREE_PEOPLE . ' people. Upgrade to Premium to talk to more.',
        ];
    }
}
