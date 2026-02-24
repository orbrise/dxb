<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppRotationMessage extends Model
{
    protected $table = 'whatsapp_rotation_messages';
    
    protected $fillable = [
        'message',
        'is_active',
        'order'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];
    
    /**
     * Scope for active messages only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope to order by the order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
    }
    
    /**
     * Get the next message for a profile (rotation logic)
     * 
     * @param int $profileId
     * @param string $profileUrl
     * @return string|null
     */
    public static function getNextMessageForProfile(int $profileId, string $profileUrl): ?string
    {
        // Get all active messages ordered
        $messages = self::active()->ordered()->get();
        
        if ($messages->isEmpty()) {
            return null;
        }
        
        // Get or create tracker for this profile
        $tracker = WhatsAppProfileMessageTracker::firstOrCreate(
            ['profile_id' => $profileId],
            ['last_message_id' => null, 'click_count' => 0]
        );
        
        // Find the next message
        $nextMessage = null;
        
        if ($tracker->last_message_id === null) {
            // First click - use first message
            $nextMessage = $messages->first();
        } else {
            // Find current message position and get next one
            $currentIndex = $messages->search(function ($msg) use ($tracker) {
                return $msg->id === $tracker->last_message_id;
            });
            
            if ($currentIndex === false || $currentIndex >= $messages->count() - 1) {
                // If message not found or at end, wrap around to first
                $nextMessage = $messages->first();
            } else {
                // Get next message in sequence
                $nextMessage = $messages->get($currentIndex + 1);
            }
        }
        
        if (!$nextMessage) {
            return null;
        }
        
        // Update tracker
        $tracker->update([
            'last_message_id' => $nextMessage->id,
            'click_count' => $tracker->click_count + 1
        ]);
        
        // Replace {url} placeholder with actual URL
        return str_replace('{url}', $profileUrl, $nextMessage->message);
    }
}
