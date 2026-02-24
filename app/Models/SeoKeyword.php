<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoKeyword extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'page', 
        'gender_id', 
        'city_id', 
        'country_id', 
        'title', 
        'keywords', 
        'description', 
        'content'
    ];

    // Relationships
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Scope for finding SEO by parameters
    public function scopeByParameters($query, $genderId = null, $cityId = null, $countryId = null)
    {
        return $query->where('gender_id', $genderId)
                    ->where('city_id', $cityId)
                    ->where('country_id', $countryId);
    }
}
