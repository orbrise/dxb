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
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        ]);
    }

    /**
     * Check if a user is part of this conversation
     */
    public function hasUser(int $userId): bool
    {
        return $this->user_one_id === $userId || $this->user_two_id === $userId;
    }

    /**
     * Get the other user in the conversation
     */
    public function getOtherUser(int $currentUserId): ?User
    {
        $otherId = $this->user_one_id === $currentUserId 
            ? $this->user_two_id 
            : $this->user_one_id;
        
        return User::find($otherId);
    }

    /**
     * Get the other user ID
     */
    public function getOtherUserId(int $currentUserId): int
    {
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
     * Get unread count for a specific user
     */
    public function getUnreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', 'unread');
            })
            ->count();
    }

    /**
     * Scope: Get conversations for a user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId);
    }
}
