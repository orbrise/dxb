<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    
    /**
     * Typeahead lookup for the auction city chooser: prefix matches first,
     * then alphabetical. Shared by the JSON endpoint and the server-side
     * (?q=) fallback so both return identical ordering.
     */
    public static function searchByName(string $term, int $limit = 8)
    {
        return static::where('name', 'like', "%{$term}%")
            ->orderByRaw('name LIKE ? DESC', ["{$term}%"])
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'country', 'slug']);
    }

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
