<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScraperAutoCity extends Model
{
    protected $fillable = [
        'source',
        'city_slug',
        'city_name',
        'limit_per_run',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'limit_per_run' => 'integer',
    ];
}
