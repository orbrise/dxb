<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'media_path',
        'media_mime',
        'media_size',
        'content',
        'background_color',
        'text_color',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(StatusView::class);
    }

    /**
     * Statuses expire 24 hours after posting — WhatsApp behavior.
     * Rather than deleting on a cron we just filter on read.
     */
    public function scopeActive($query)
    {
        return $query->where('created_at', '>', now()->subDay());
    }

    public function isExpired(): bool
    {
        return $this->created_at->lt(now()->subDay());
    }

    public function viewedBy(int $userId): bool
    {
        return $this->views()->where('viewer_id', $userId)->exists();
    }
}
