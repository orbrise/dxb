<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScraperRun extends Model
{
    protected $fillable = [
        'source',
        'city_slug',
        'requested_count',
        'status',
        'progress_current',
        'progress_total',
        'progress_stage',
        'exit_code',
        'log_path',
        'error_message',
        'started_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_FAILED], true);
    }

    public function progressPercent(): int
    {
        $total = (int) $this->progress_total ?: (int) $this->requested_count;
        if ($total <= 0) return 0;
        return min(100, (int) floor(($this->progress_current / $total) * 100));
    }
}
