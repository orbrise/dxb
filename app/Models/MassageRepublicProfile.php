<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MassageRepublicProfile extends Model
{
    use HasFactory;

    protected $table = 'massage_republic_profiles';

    protected $fillable = [
        'external_id',
        'profile_url',
        'email',
        'name',
        'age',
        'city',
        'phone',
        'website',
        'gender',
        'rating',
        'services',
        'description',
        'image_urls',
        'attributes',
        'incall_price',
        'outcall_price',
        'incall_currency',
        'outcall_currency',
        'raw_html',
        'scraped_at',
        'source_city',
        'imported_user_id',
        'imported_profile_id',
        'imported_at',
    ];

    protected $casts = [
        'image_urls' => 'array',
        'attributes' => 'array',
        'scraped_at' => 'datetime',
        'imported_at' => 'datetime',
    ];
}
