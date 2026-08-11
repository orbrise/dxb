<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IvySocieteProfile extends Model
{
    use HasFactory;

    protected $table = 'ivysociete_profiles';

    protected $fillable = [
        'external_id',
        'profile_url',
        'name',
        'age',
        'city',
        'phone',
        'email',
        'website',
        'gender',
        'is_verified',
        'is_premium',
        'description',
        'image_urls',
        'attributes',
        'incall_price',
        'outcall_price',
        'incall_currency',
        'outcall_currency',
        'scraped_at',
        'source_city',
        'imported_user_id',
        'imported_profile_id',
        'imported_at',
    ];

    protected $casts = [
        'image_urls' => 'array',
        'attributes' => 'array',
        'is_verified' => 'boolean',
        'is_premium' => 'boolean',
        'scraped_at' => 'datetime',
        'imported_at' => 'datetime',
    ];
}
