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
        'reply_to_id',
        'status',
        'replied_at',
        'attachment_path',
        'attachment_disk',
        'attachment_thumbnail_path',
        'attachment_type',
        'attachment_mime',
        'attachment_size',
        'attachment_duration',
        'attachment_original_name',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'replied_at' => 'datetime',
        'expires_at' => 'datetime',
        'attachment_size' => 'integer',
        'attachment_duration' => 'integer',
    ];

    /**
     * Model hooks. Any new message or status change (unread → read) must
     * invalidate the recipient's badge cache so the chat/calls icon in the
     * top header + mobile nav shows the right count without waiting out
     * the 30s TTL. Covers every write path — Chat::sendReply,
     * ChatMediaController::uploadVoice/uploadAttachment, forwardTo,
     * admin replies — so we don't have to remember to clear per call site.
     */
    protected static function booted(): void
    {
        $invalidateBothSides = function (Message $msg) {
            try {
                $conv = $msg->conversation;
                if (!$conv) return;
                $senderId = (int) $msg->sender_id;
                $uids = array_filter([
                    (int) $conv->user_one_id,
                    (int) $conv->user_two_id,
                    $senderId,
                ]);
                $uids = array_values(array_unique($uids));

                foreach ($uids as $uid) {
                    // Badge counter (top header + mobile nav) — clear
                    // so the next /chat/badges poll returns fresh values.
                    \App\Support\ChatBadges::clear($uid);

                    // Sidebar conversation list cache (unread pill, last
                    // message preview, Unread filter counter). Without
                    // this the sidebar stays stale until the 5s cache
                    // TTL expires AND the next tickPoll fires.
                    foreach (['chat.convs', 'chat.statusfeed', 'chat.gallery', 'chat.calls'] as $prefix) {
                        \Illuminate\Support\Facades\Cache::forget("{$prefix}.{$uid}");
                    }
                    foreach (['0', '1'] as $pref) {
                        // The getConversations key uses md5(searchTerm) +
                        // the pref flag — clear the empty-search variant
                        // (hot path; search is rare).
                        \Illuminate\Support\Facades\Cache::forget(
                            "chat.convs.{$uid}." . md5('') . ".{$pref}"
                        );
                    }
                }
            } catch (\Throwable $e) {
                // Non-critical — stale badge for up to 30s is tolerable.
            }
        };

        static::created($invalidateBothSides);
        static::updated($invalidateBothSides);
        static::deleted($invalidateBothSides);
    }

    /**
     * Filter out disappearing messages that have passed their expiry.
     * Applied everywhere we render messages so expired ones vanish from
     * history without needing a cron.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Translate a WhatsApp-style TTL preset ('never'|'24h'|'1w'|'1m') to
     * a Carbon expires_at, or null for "never".
     */
    public static function ttlToExpiry(?string $ttl, ?\Carbon\Carbon $from = null): ?\Carbon\Carbon
    {
        $from = $from ?: now();
        return match ($ttl) {
            '24h'   => $from->copy()->addDay(),
            '1w'    => $from->copy()->addWeek(),
            '1m'    => $from->copy()->addMonth(),
            default => null, // 'never' / null / unknown
        };
    }

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
     * Public URL for the video poster thumbnail, if one was captured and
     * uploaded alongside the video. For video attachments only; null for
     * everything else (images are their own poster, voice notes don't
     * have one). Served through the same /chat/media/{id}/thumb route
     * so the permission check applies to the thumbnail too.
     */
    public function getAttachmentThumbnailUrlAttribute(): ?string
    {
        if (!$this->attachment_thumbnail_path || !$this->id) return null;
        return route('chat.media.thumb', $this->id);
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

    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_id');
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
