<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SupportQuickReply extends Model
{
    protected $fillable = [
        'shortcut',
        'message',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * "/Thank You " → "thank-you". Agents may type the leading slash or
     * spaces; the stored shortcut is always the bare lowercase keyword.
     */
    public static function normalizeShortcut(string $shortcut): string
    {
        return (string) Str::of($shortcut)->trim()->ltrim('/')->lower()->replaceMatches('/\s+/', '-');
    }
}
