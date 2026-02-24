<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileImage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'profile_id', 'image', 'random', 'is_main', 'image_webp', 'image_order'];
}
