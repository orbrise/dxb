<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_id', 
        'review',
        'star',
        'status',
        'reply'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getuser(){
        return $this->hasOne(UsersProfile::class, 'id', 'profile_id');
    }

    public function getpic(){
        return $this->hasOne(ProfileImage::class, 'profile_id', 'profile_id');
    }

    // Relationship to the profile being reviewed
    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id', 'id');
    }

    // Relationship to the user who wrote the review
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
