<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';
    
    protected $fillable = [
        'conversation_id',
        'message',
        'direction', // incoming, outgoing
        'status', // sent, delivered, read, failed
        'whatsapp_message_id',
        'is_read',
        'sent_at',
        'delivered_at',
        'read_at',
        'received_at'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'received_at' => 'datetime'
    ];
    
    /**
     * Get the conversation
     */
    public function conversation()
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }
    
    /**
     * Check if message is outgoing
     */
    public function isOutgoing()
    {
        return $this->direction === 'outgoing';
    }
    
    /**
     * Check if message is incoming
     */
    public function isIncoming()
    {
        return $this->direction === 'incoming';
    }
}
