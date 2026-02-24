<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageCountryPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'country_id',
        'price_tiers'
    ];

    protected $casts = [
        'price_tiers' => 'array'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
