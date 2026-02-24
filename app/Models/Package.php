<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;


    protected $fillable = [
        'id',
        'name',
        'price',
        'promo_days',
        'description',
        'status',
        'created_at',
        'updated_at',
        'price_tiers',
        'tagline',
        'is_global'
    ];

    protected $casts = [
        'price_tiers' => 'array',
        'is_global' => 'boolean',
    ];

    public function profiles()
    {
        return $this->hasMany(UsersProfile::class);
    }

    public function countryPrices()
    {
        return $this->hasMany(PackageCountryPrice::class);
    }

    /**
     * Get price tiers for specific country or default
     * @param int|Country $country Country ID or Country model
     * @return array Price tiers array
     */
    public function getPriceForCountry($country = null)
    {
        // If package is global, return the global price tiers
        if ($this->is_global) {
            return json_decode($this->price_tiers, true) ?: [];
        }
        
        // If no country provided, get from domain
        if (!$country) {
            $country = getCurrentCountry();
        }
        
        // Get country ID
        $countryId = is_object($country) ? $country->id : $country;
        
        // Try to find country-specific pricing
        if ($countryId) {
            $countryPrice = $this->countryPrices()
                ->where('country_id', $countryId)
                ->first();
            
            if ($countryPrice && $countryPrice->price_tiers) {
                return json_decode($countryPrice->price_tiers, true);
            }
        }
        
        // Return default price tiers
        return json_decode($this->price_tiers, true) ?: [];
    }

    /**
     * Get formatted price tiers with currency for specific country
     * @param int|Country $country
     * @return array
     */
    public function getFormattedPrices($country = null)
    {
        $tiers = $this->getPriceForCountry($country);
        $formatted = [];
        
        foreach ($tiers as $tier) {
            $formatted[] = [
                'days' => $tier['days'],
                'price' => $tier['price'],
                'formatted' => '$' . number_format($tier['price'], 2)
            ];
        }
        
        return $formatted;
    }
}
