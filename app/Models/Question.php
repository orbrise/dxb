<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'profile_id',
        'question',
        'status',
        'answer',
        'answer_status'
    ];

    public function getuser(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id', 'id');
    }

    // Relationship to the user who asked the question
    public function askedBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
