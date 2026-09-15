<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallSignal extends Model
{
    // Match the migration — we only stamp created_at.
    public $timestamps = false;

    protected $fillable = [
        'call_id',
        'from_user_id',
        'to_user_id',
        'type',
        'call_type',
        'payload',
        'delivered_at',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'delivered_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
