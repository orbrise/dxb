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
    ];

    protected $casts = [
        'auto_archive_enabled' => 'boolean',
        'send_archive_warning' => 'boolean',
        'auto_delete_archived_enabled' => 'boolean',
        'auto_delete_inactive_enabled' => 'boolean',
        'geo_redirect_enabled' => 'boolean',
    ];
}
