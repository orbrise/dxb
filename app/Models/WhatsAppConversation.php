<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';
    
    protected $fillable = [
        'phone_number',
        'name',
        'last_message_at',
        'notes'
    ];
    
    protected $casts = [
        'last_message_at' => 'datetime'
    ];
    
    /**
     * Get messages for this conversation
     */
    public function messages()
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }
    
    /**
     * Get unread messages count
     */
    public function getUnreadCountAttribute()
    {
        return $this->messages()
            ->where('direction', 'incoming')
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Get last message
     */
    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }
}
