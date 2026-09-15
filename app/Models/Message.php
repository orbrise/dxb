<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'user_email',
        'profile_id',
        'message',
        'code',
        'phone',
        'reply',
        'status',
        'replied_at',
        'attachment_path',
        'attachment_type',
        'attachment_mime',
        'attachment_size',
        'attachment_duration',
        'attachment_original_name',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'replied_at' => 'datetime',
        'attachment_size' => 'integer',
        'attachment_duration' => 'integer',
    ];

    /**
     * Full public URL to the attachment (for use in <img>, <a href>, <audio>).
     *
     * ALL attachment types go through the chat.media.serve route:
     *  - forces the correct Content-Type (some hosts don't map .webm)
     *  - enforces the conversation-participant permission check
     *  - doesn't rely on a public/storage symlink (which is missing on
     *    this host — public/storage is a real directory used for other
     *    assets, not a symlink to storage/app/public)
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path || !$this->id) return null;
        return route('chat.media.serve', $this->id);
    }

    /**
     * Convenience: is this message just a media message?
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get the conversation this message belongs to
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender of the message
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the profile that owns the message (legacy)
     */
    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }

    /**
     * Check if message was sent by a specific user
     */
    public function isSentBy(int $userId): bool
    {
        return $this->sender_id === $userId;
    }

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread')->orWhereNull('status');
    }

    /**
     * Scope to get messages for a specific profile
     */
    public function scopeForProfile($query, $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    /**
     * Scope to get messages for a conversation
     */
    public function scopeForConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }
}
