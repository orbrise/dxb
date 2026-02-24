<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLanguage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'language_id', 'expert', 'profile_id'];

    public function getlangname() {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
