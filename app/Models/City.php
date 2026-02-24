<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    
    /**
     * Get the country that the city belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the SEO keywords for the city.
     */
    public function seoKeywords()
    {
        return $this->hasMany(SeoKeyword::class);
    }
}
