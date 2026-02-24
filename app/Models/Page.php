<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'is_published', 'order_index'];

    /**
     * Get pages ordered by order_index
     */
    public static function ordered()
    {
        return static::orderBy('order_index', 'asc')->orderBy('id', 'asc');
    }
}
