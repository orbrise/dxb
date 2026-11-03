<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportBot extends Model
{
    use HasFactory;

    public const TASK_SUPPORT_CHAT = 'support_chat';

    public const TASKS = [
        self::TASK_SUPPORT_CHAT => 'Support chat (draft replies)',
    ];

    protected $fillable = [
        'name',
        'description',
        'task',
        'system_prompt',
        'knowledge',
        'model',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTask($query, string $task)
    {
        return $query->where('task', $task);
    }
}
