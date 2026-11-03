<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'keywords',
        'title',
        'description',
        'app_logo',
        'favicon',
        'admin_bg',
        'collapse_icon',
        'call_ringtone_path',
        'auto_archive_enabled',
        'auto_archive_days',
        'send_archive_warning',
        'archive_warning_days',
        // New auto-delete settings
        'auto_delete_archived_enabled',
        'auto_delete_archived_days',
        'auto_delete_inactive_enabled',
        'auto_delete_inactive_days',
        // Geo redirect setting
        'geo_redirect_enabled',
        // Hide profile pics from US visitors
        'hide_us_profile_pics',
        'support_calls_blocked',
        'support_auto_reply_enabled',
        'support_auto_reply_delay',
        'support_auto_reply_message',
        'support_auto_reply_sender_id',
    ];

    protected $casts = [
        'auto_archive_enabled' => 'boolean',
        'send_archive_warning' => 'boolean',
        'auto_delete_archived_enabled' => 'boolean',
        'auto_delete_inactive_enabled' => 'boolean',
        'geo_redirect_enabled' => 'boolean',
        'hide_us_profile_pics' => 'boolean',
        'support_calls_blocked' => 'boolean',
        'support_auto_reply_enabled' => 'boolean',
        'support_auto_reply_delay' => 'integer',
    ];
}
