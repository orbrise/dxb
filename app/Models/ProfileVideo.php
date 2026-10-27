<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Mirrors ProfileImage. The accessor pair (video_url + thumbnail_url) hides
 * the URL assembly from the views so blade can just do `{{ $video->video_url }}`.
 */
class ProfileVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id',
        'video',
        'video_thumbnail',
        'mime',
        'size_bytes',
        'duration_seconds',
        'is_main',
        'video_order',
    ];

    protected $casts = [
        'is_main'          => 'boolean',
        'size_bytes'       => 'integer',
        'duration_seconds' => 'integer',
        'video_order'      => 'integer',
    ];

    /**
     * Full CDN URL to the stored MP4/WebM.
     * Base path mirrors userimages/: uservideos/{user_id}/{profile_id}/{filename}
     */
    public function getVideoUrlAttribute(): ?string
    {
        if (!$this->video) return null;
        $base = rtrim(env('ASSETS_EXTERNAL_URL', 'https://assets.evoory.com'), '/');
        return "{$base}/uservideos/{$this->user_id}/{$this->profile_id}/{$this->video}";
    }

    /**
     * Full CDN URL to the captured first-frame JPEG. Null when a thumbnail
     * wasn't captured (older rows or client-side capture failed) — views
     * should fall back to a `<video preload="metadata">` poster.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->video_thumbnail) return null;
        $base = rtrim(env('ASSETS_EXTERNAL_URL', 'https://assets.evoory.com'), '/');
        return "{$base}/uservideos/{$this->user_id}/{$this->profile_id}/{$this->video_thumbnail}";
    }

    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
