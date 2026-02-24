<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    /**
     * Display the Quick Telegram redirect page
     */
    public function quickChat()
    {
        // Get recent quick chat history from session
        $recentChats = session('telegram_quick_chats', []);
        
        return view('admin.telegram.quick-chat', compact('recentChats'));
    }
    
    /**
     * Generate Telegram URL and save to history
     */
    public function generateLink(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
        ]);
        
        // Clean phone number (remove spaces, dashes, etc.)
        $countryCode = preg_replace('/[^0-9]/', '', $request->country_code);
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $fullPhone = $countryCode . $phone;
        
        // Build Telegram URL
        $url = "https://t.me/+{$fullPhone}";
        
        // Save to session history (max 10 recent)
        $recentChats = session('telegram_quick_chats', []);
        $newChat = [
            'country_code' => $countryCode,
            'phone' => $phone,
            'full_phone' => $fullPhone,
            'url' => $url,
            'created_at' => now()->toDateTimeString()
        ];
        
        // Add to beginning and limit to 10
        array_unshift($recentChats, $newChat);
        $recentChats = array_slice($recentChats, 0, 10);
        
        session(['telegram_quick_chats' => $recentChats]);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'full_phone' => $fullPhone
        ]);
    }
    
    /**
     * Clear the quick chat history
     */
    public function clearHistory()
    {
        session()->forget('telegram_quick_chats');
        
        return response()->json(['success' => true]);
    }
}
