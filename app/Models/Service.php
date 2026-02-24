<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug'];
    
    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($service) {
            if (empty($service->slug) || $service->isDirty('name')) {
                $service->slug = Str::slug($service->name);
            }
        });
    }
    
    /**
     * Profiles that offer this service
     */
    public function profiles()
    {
        return $this->belongsToMany(UsersProfile::class, 'user_services', 'service_id', 'profile_id');
    }
}
