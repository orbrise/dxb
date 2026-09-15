<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at',
        'is_support',
        'is_pinned',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_support' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    /**
     * Get or create a conversation between two users
     */
    public static function getOrCreate(int $userOneId, int $userTwoId): self
    {
        // Always store with lower ID first for consistency
        $ids = [$userOneId, $userTwoId];
        sort($ids);

        return self::firstOrCreate([
            'user_one_id' => $ids[0],
            'user_two_id' => $ids[1],
            'is_support' => false,
        ]);
    }

    /**
     * Get or create the pinned Support conversation for a user.
     * Support conversations have user_two_id = NULL and any admin can reply.
     */
    public static function getOrCreateSupport(int $userId): self
    {
        return self::firstOrCreate(
            [
                'user_one_id' => $userId,
                'is_support' => true,
            ],
            [
                'user_two_id' => null,
                'is_pinned' => true,
            ]
        );
    }

    /**
     * Check if a user is part of this conversation
     */
    public function hasUser(int $userId): bool
    {
        return $this->user_one_id === $userId || $this->user_two_id === $userId;
    }

    /**
     * Get the other user in the conversation. For support conversations this
     * returns null (the "other side" is any admin, not a specific user).
     */
    public function getOtherUser(int $currentUserId): ?User
    {
        if ($this->is_support) {
            return null;
        }

        $otherId = $this->user_one_id === $currentUserId
            ? $this->user_two_id
            : $this->user_one_id;

        return $otherId ? User::find($otherId) : null;
    }

    /**
     * Get the other user ID. Returns null for support conversations.
     */
    public function getOtherUserId(int $currentUserId): ?int
    {
        if ($this->is_support) {
            return null;
        }

        return $this->user_one_id === $currentUserId
            ? $this->user_two_id
            : $this->user_one_id;
    }

    /**
     * Relationship: User One
     */
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    /**
     * Relationship: User Two
     */
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    /**
     * Relationship: Messages
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get unread count for a specific user. A message is unread until Chat::markAsRead
     * stamps it 'read' on open — that method treats NULL, 'sent', 'delivered', and
     * 'unread' all as unread, so we must too. Previously this only counted NULL /
     * 'unread', so freshly sent messages (which land as 'sent' or 'delivered') were
     * invisible to per-conversation badges and to /my-account's Messages badge.
     */
    public function getUnreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where(function($q) {
                $q->whereNull('status')->orWhereIn('status', ['sent', 'delivered', 'unread']);
            })
            ->count();
    }

    /**
     * Scope: Get conversations for a user (both 1:1 and support).
     * Grouped so an outer ->where() can't be swallowed by the OR.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_one_id', $userId)
              ->orWhere('user_two_id', $userId);
        });
    }

    /**
     * Scope: only support conversations.
     */
    public function scopeSupport($query)
    {
        return $query->where('is_support', true);
    }
}
