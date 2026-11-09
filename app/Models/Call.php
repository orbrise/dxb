<?php

namespace App\Models;

use App\Events\NewChatMessage;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Call extends Model
{
    protected $fillable = [
        'call_id',
        'caller_id',
        'callee_id',
        'conversation_id',
        'type',
        'is_support',
        'status',
        'started_at',
        'connected_at',
        'ended_at',
        'duration',
        'seen_at',
        'caller_hidden_at',
        'callee_hidden_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'connected_at' => 'datetime',
        'ended_at' => 'datetime',
        'seen_at' => 'datetime',
        'caller_hidden_at' => 'datetime',
        'callee_hidden_at' => 'datetime',
        'duration' => 'integer',
        'is_support' => 'boolean',
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
     * Calls in the given user's own call history: ones they took part in
     * and haven't deleted from their side.
     */
    public function scopeInHistoryOf($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where(fn ($q) => $q->where('caller_id', $userId)->whereNull('caller_hidden_at'))
              ->orWhere(fn ($q) => $q->where('callee_id', $userId)->whereNull('callee_hidden_at'));
        });
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

    /**
     * Support calls nobody picked up can be left "ringing" for good: the
     * caller closed the tab or lost signal before their hang-up reached us.
     * The caller's own no-answer timers end a call within 45 s, so one still
     * ringing after two minutes was missed. Log it as such, with its call
     * row in the support chat at the time it rang.
     */
    public static function closeAbandonedSupportCalls(): void
    {
        $abandoned = static::where('is_support', true)
            ->where('status', 'ringing')
            ->where('created_at', '<', now()->subMinutes(2))
            ->limit(50)
            ->get();

        foreach ($abandoned as $call) {
            // Conditional update, so two admin screens closing the same call
            // at once can't both post its chat row.
            $closed = static::whereKey($call->id)->where('status', 'ringing')->update(['status' => 'missed']);
            if (!$closed) continue;

            $call->status = 'missed';
            $call->syncOriginalAttribute('status');
            $call->postSummaryMessage($call->started_at ?? $call->created_at);
        }
    }

    /**
     * Insert a WhatsApp-style call-summary message into the associated
     * conversation so both participants see the call outcome in their thread.
     * Reuses the existing attachment_* columns (no schema change):
     *
     *   attachment_type     = 'call'
     *   attachment_mime     = 'audio' | 'video'         (the call kind)
     *   attachment_original_name = final Call.status    (missed|declined|ended)
     *   attachment_duration = seconds when status=ended (nullable otherwise)
     *
     * The chat view renders this as a call bubble with a phone icon,
     * direction arrow, status text and (if answered) duration. $at dates a
     * call closed after the fact back to when it rang.
     */
    public function postSummaryMessage(?CarbonInterface $at = null): void
    {
        if (!$this->conversation_id) {
            // Missing — try to look up the 1:1 conversation between the pair.
            $conv = Conversation::forUser($this->caller_id)
                ->where(function ($q) {
                    $q->where('user_one_id', $this->callee_id)
                      ->orWhere('user_two_id', $this->callee_id);
                })
                ->where('is_support', false)
                ->first();
            if (!$conv) return;
            $this->conversation_id = $conv->id;
            $this->save();
        }

        $at ??= now();

        try {
            $message = new Message([
                'conversation_id' => $this->conversation_id,
                'sender_id' => $this->caller_id,
                'message' => '',
                'status' => 'sent',
                'attachment_type' => 'call',
                'attachment_mime' => $this->type,              // 'audio' | 'video'
                'attachment_original_name' => $this->status,   // 'missed' | 'declined' | 'ended'
                'attachment_duration' => $this->duration,
            ]);
            $message->created_at = $at;
            $message->updated_at = $at;
            $message->save();

            // Never moves a chat's last activity back in time.
            Conversation::whereKey($this->conversation_id)
                ->where(fn ($q) => $q->whereNull('last_message_at')->orWhere('last_message_at', '<', $at))
                ->update(['last_message_at' => $at]);

            // Broadcast to the callee so the message appears in real time.
            // A support call nobody picked up has no callee: 0 sends it to
            // the support inbox.
            try {
                broadcast(new NewChatMessage($message, (int) $this->callee_id))->toOthers();
            } catch (\Throwable $e) {
                Log::channel('rtc')->warning('call_summary.broadcast_failed', [
                    'call_id' => $this->call_id,
                    'error' => $e->getMessage(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('rtc')->error('call_summary.insert_failed', [
                'call_id' => $this->call_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * The call's row in its chat. Not linked by id: it's the caller's first
     * call row posted once the call started, and calls from one caller
     * never overlap.
     */
    public function summaryMessageId(): ?int
    {
        if (!$this->conversation_id) return null;

        $from = $this->started_at ?? $this->created_at;

        return Message::where('conversation_id', $this->conversation_id)
            ->where('sender_id', $this->caller_id)
            ->where('attachment_type', 'call')
            ->where('created_at', '>=', $from->copy()->subSeconds(5))
            ->where('created_at', '<=', ($this->ended_at ?? $from)->copy()->addMinutes(5))
            ->orderBy('created_at')
            ->orderBy('id')
            ->value('id');
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
