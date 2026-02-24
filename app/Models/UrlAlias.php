<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlAlias extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'custom_url',
        'base_pattern', 
        'redirect_type',
        'description',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    /**
     * Find alias by custom URL
     */
    public static function findByCustomUrl($url)
    {
        $url = trim($url, '/');
        return static::where('custom_url', $url)
                    ->where('is_active', true)
                    ->first();
    }
    
    /**
     * Get all active aliases
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
