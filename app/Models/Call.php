<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = [
        'call_id',
        'caller_id',
        'callee_id',
        'conversation_id',
        'type',
        'status',
        'started_at',
        'connected_at',
        'ended_at',
        'duration',
        'seen_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'connected_at' => 'datetime',
        'ended_at' => 'datetime',
        'seen_at' => 'datetime',
        'duration' => 'integer',
    ];

    /**
     * Model hooks — clear the badge cache for both participants whenever
     * a call row is created / updated (missed → seen, incoming → missed,
     * etc.) so the top-header and mobile-nav badges reflect reality
     * within one poll tick instead of waiting out the 30s TTL.
     */
    protected static function booted(): void
    {
        $clear = function (Call $call) {
            try {
                foreach ([$call->caller_id, $call->callee_id] as $uid) {
                    if (!$uid) continue;
                    \App\Support\ChatBadges::clear((int) $uid);
                    // Also bust Chat::getCallHistory()'s 10s cache so a
                    // freshly-written row surfaces in the Calls tab on the
                    // next render instead of waiting out the TTL. Keyed
                    // per-user to match Cache::remember("chat.calls.{uid}").
                    \Illuminate\Support\Facades\Cache::forget("chat.calls.{$uid}");
                }
            } catch (\Throwable $e) {
                // Stale badge / list for up to 30s is tolerable; swallow.
            }
        };
        static::created($clear);
        static::updated($clear);
        static::deleted($clear);
    }

    /**
     * Missed calls the given user (as callee) has not yet acknowledged.
     */
    public function scopeUnseenMissedFor($query, int $userId)
    {
        return $query->where('callee_id', $userId)
            ->where('status', 'missed')
            ->whereNull('seen_at');
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function callee()
    {
        return $this->belongsTo(User::class, 'callee_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
