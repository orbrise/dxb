<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    
    /**
     * Get the nationality name (alias for nicename for backward compatibility)
     */
    public function getNationalityAttribute()
    {
        return $this->nicename;
    }
}
