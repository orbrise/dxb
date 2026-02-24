<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlogSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'sort_order',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("blog_setting_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->value = $value;
            $setting->save();
            Cache::forget("blog_setting_{$key}");
            return true;
        }

        return false;
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray(): array
    {
        return Cache::remember('blog_settings_all', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group)
    {
        return self::where('group', $group)->orderBy('sort_order')->get();
    }

    /**
     * Clear all blog settings cache
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("blog_setting_{$setting->key}");
        }
        Cache::forget('blog_settings_all');
    }
}
