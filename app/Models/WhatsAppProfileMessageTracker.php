<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppProfileMessageTracker extends Model
{
    protected $table = 'whatsapp_profile_message_tracker';
    
    protected $fillable = [
        'profile_id',
        'last_message_id',
        'click_count' 
    ];
    
    protected $casts = [
        'click_count' => 'integer'
    ];
    
    /**
     * Get the profile
     */
    public function profile()
    {
        return $this->belongsTo(UsersProfile::class, 'profile_id');
    }
    
    /**
     * Get the last message used
     */
    public function lastMessage()
    {
        return $this->belongsTo(WhatsAppRotationMessage::class, 'last_message_id');
    }
}
