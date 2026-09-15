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
