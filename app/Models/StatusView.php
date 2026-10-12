<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusView extends Model
{
    use HasFactory;

    // The status_views table only has viewed_at; it has no
    // created_at / updated_at columns. Leaving Eloquent's default
    // timestamps on causes "Unknown column 'updated_at'" on every
    // insert when the Chat component records a status view.
    public $timestamps = false;

    protected $fillable = [
        'status_id',
        'viewer_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }
}
