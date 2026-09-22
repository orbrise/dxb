<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'p256dh_key',
        'auth_key',
        'content_encoding',
        'user_agent',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Shape the row for the minishlink/web-push Subscription factory.
     */
    public function toWebPush(): array
    {
        return [
            'endpoint'        => $this->endpoint,
            'publicKey'       => $this->p256dh_key,
            'authToken'       => $this->auth_key,
            'contentEncoding' => $this->content_encoding ?: 'aesgcm',
        ];
    }
}
