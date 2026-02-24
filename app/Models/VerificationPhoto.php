<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationPhoto extends Model
{
    protected $fillable = [
        'user_id',
        'profile_id', 
        'photo',
        'status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'rejection_link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }
}