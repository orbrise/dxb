<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultSeoSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'title',
        'description',
        'keywords',
        'content',
        'is_active',
        'priority'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
    
    // Get default SEO by name
    public static function getByName($name)
    {
        return self::where('name', $name)->where('is_active', true)->first();
    }
    
    // Get the highest priority default SEO
    public static function getDefault()
    {
        return self::active()->byPriority()->first();
    }
}
