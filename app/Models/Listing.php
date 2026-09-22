<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'slug', 'is_escort_category', 'sort_order'];

    protected $casts = [
        'is_escort_category' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function bySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
