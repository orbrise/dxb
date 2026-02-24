<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    /**
     * Display the Quick WhatsApp Web redirect page
     */
    public function quickChat()
    {
        // Get recent quick chat history from session
        $recentChats = session('whatsapp_quick_chats', []);
        
        return view('admin.whatsapp.quick-chat', compact('recentChats'));
    }
    
    /**
     * Generate WhatsApp Web URL and save to history
     */
    public function generateLink(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:5',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:500'
        ]);
        
        // Clean phone number (remove spaces, dashes, etc.)
        $countryCode = preg_replace('/[^0-9]/', '', $request->country_code);
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        $fullPhone = $countryCode . $phone;
        
        // Build WhatsApp URL
        $url = "https://wa.me/{$fullPhone}";
        if ($request->message) {
            $url .= "?text=" . urlencode($request->message);
        }
        
        // Save to session history (max 10 recent)
        $recentChats = session('whatsapp_quick_chats', []);
        $newChat = [
            'country_code' => $countryCode,
            'phone' => $phone,
            'full_phone' => $fullPhone,
            'message' => $request->message,
            'url' => $url,
            'created_at' => now()->format('Y-m-d H:i:s')
        ];
        
        // Remove duplicate if exists
        $recentChats = array_filter($recentChats, fn($chat) => $chat['full_phone'] !== $fullPhone);
        
        // Add to beginning
        array_unshift($recentChats, $newChat);
        
        // Keep only last 10
        $recentChats = array_slice($recentChats, 0, 10);
        
        session(['whatsapp_quick_chats' => $recentChats]);
        
        return response()->json([
            'success' => true,
            'url' => $url,
            'phone' => $fullPhone
        ]);
    }
    
    /**
     * Clear quick chat history
     */
    public function clearHistory()
    {
        session()->forget('whatsapp_quick_chats');
        return response()->json(['success' => true]);
    }
    
    /**
     * Display the WhatsApp chat interface
     */
    public function index()
    {
        $conversations = WhatsAppConversation::with(['messages' => function($q) {
            $q->latest()->limit(1);
        }])
        ->orderBy('last_message_at', 'desc')
        ->get();
        
        return view('admin.whatsapp.index', compact('conversations'));
    }
    
    /**
     * Get messages for a specific conversation
     */
    public function getMessages($conversationId)
    {
        $conversation = WhatsAppConversation::findOrFail($conversationId);
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read
        $conversation->messages()->where('is_read', false)->update(['is_read' => true]);
        
        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }
    
    /**
     * Start a new conversation
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'name' => 'nullable|string|max:100'
        ]);
        
        $phoneNumber = $this->formatPhoneNumber($request->phone_number);
        
        // Check if conversation already exists
        $conversation = WhatsAppConversation::where('phone_number', $phoneNumber)->first();
        
        if (!$conversation) {
            $conversation = WhatsAppConversation::create([
                'phone_number' => $phoneNumber,
                'name' => $request->name ?? 'Unknown',
                'last_message_at' => now()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'conversation' => $conversation
        ]);
    }
    
    /**
     * Send a WhatsApp message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:whatsapp_conversations,id',
            'message' => 'required|string|max:4096'
        ]);
        
        $conversation = WhatsAppConversation::findOrFail($request->conversation_id);
        
        // Send via WhatsApp API
        $result = $this->sendWhatsAppMessage($conversation->phone_number, $request->message);
        
        if ($result['success']) {
            // Store message in database
            $message = WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'message' => $request->message,
                'direction' => 'outgoing',
                'status' => 'sent',
                'whatsapp_message_id' => $result['message_id'] ?? null,
                'sent_at' => now()
            ]);
            
            // Update conversation
            $conversation->update(['last_message_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Failed to send message'
        ], 400);
    }
    
    /**
     * Send quick message to a phone number (without conversation)
     */
    public function sendQuickMessage(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string|max:4096',
            'name' => 'nullable|string|max:100'
        ]);
        
        $phoneNumber = $this->formatPhoneNumber($request->phone_number);
        
        // Get or create conversation
        $conversation = WhatsAppConversation::firstOrCreate(
            ['phone_number' => $phoneNumber],
            ['name' => $request->name ?? 'Unknown', 'last_message_at' => now()]
        );
        
        // Send via WhatsApp API
        $result = $this->sendWhatsAppMessage($phoneNumber, $request->message);
        
        if ($result['success']) {
            // Store message
            $message = WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'message' => $request->message,
                'direction' => 'outgoing',
                'status' => 'sent',
                'whatsapp_message_id' => $result['message_id'] ?? null,
                'sent_at' => now()
            ]);
            
            $conversation->update(['last_message_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'conversation' => $conversation
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Failed to send message'
        ], 400);
    }
    
    /**
     * Delete a conversation
     */
    public function deleteConversation($id)
    {
        $conversation = WhatsAppConversation::findOrFail($id);
        $conversation->messages()->delete();
        $conversation->delete();
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get WhatsApp settings
     */
    public function settings()
    {
        $settings = [
            'whatsapp_api_token' => config('services.whatsapp.api_token', ''),
            'whatsapp_phone_number_id' => config('services.whatsapp.phone_number_id', ''),
            'whatsapp_business_account_id' => config('services.whatsapp.business_account_id', ''),
        ];
        
        return view('admin.whatsapp.settings', compact('settings'));
    }
    
    /**
     * Update WhatsApp settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'whatsapp_api_token' => 'required|string',
            'whatsapp_phone_number_id' => 'required|string',
            'whatsapp_business_account_id' => 'nullable|string'
        ]);
        
        // Update .env file or database settings
        $this->updateEnvValue('WHATSAPP_API_TOKEN', $request->whatsapp_api_token);
        $this->updateEnvValue('WHATSAPP_PHONE_NUMBER_ID', $request->whatsapp_phone_number_id);
        $this->updateEnvValue('WHATSAPP_BUSINESS_ACCOUNT_ID', $request->whatsapp_business_account_id);
        
        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }
    
    /**
     * Webhook to receive incoming WhatsApp messages
     */
    public function webhook(Request $request)
    {
        // Verify webhook (for initial setup)
        if ($request->isMethod('get')) {
            $verifyToken = config('services.whatsapp.verify_token', 'your_verify_token');
            
            if ($request->get('hub_verify_token') === $verifyToken) {
                return response($request->get('hub_challenge'));
            }
            
            return response('Invalid verify token', 403);
        }
        
        // Handle incoming messages
        $data = $request->all();
        
        Log::info('WhatsApp Webhook:', $data);
        
        if (isset($data['entry'][0]['changes'][0]['value']['messages'])) {
            foreach ($data['entry'][0]['changes'][0]['value']['messages'] as $msg) {
                $this->processIncomingMessage($msg, $data['entry'][0]['changes'][0]['value']);
            }
        }
        
        return response('OK', 200);
    }
    
    /**
     * Process incoming WhatsApp message
     */
    protected function processIncomingMessage($msg, $value)
    {
        $phoneNumber = $msg['from'];
        $messageText = $msg['text']['body'] ?? '';
        $messageId = $msg['id'];
        
        // Get contact name if available
        $contactName = 'Unknown';
        if (isset($value['contacts'][0]['profile']['name'])) {
            $contactName = $value['contacts'][0]['profile']['name'];
        }
        
        // Get or create conversation
        $conversation = WhatsAppConversation::firstOrCreate(
            ['phone_number' => $phoneNumber],
            ['name' => $contactName, 'last_message_at' => now()]
        );
        
        // Update name if we have a better one
        if ($contactName !== 'Unknown' && $conversation->name === 'Unknown') {
            $conversation->update(['name' => $contactName]);
        }
        
        // Store message
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'message' => $messageText,
            'direction' => 'incoming',
            'status' => 'received',
            'whatsapp_message_id' => $messageId,
            'received_at' => now()
        ]);
        
        $conversation->update(['last_message_at' => now()]);
    }
    
    /**
     * Send WhatsApp message via Meta Cloud API
     */
    protected function sendWhatsAppMessage($phoneNumber, $message)
    {
        $token = config('services.whatsapp.api_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        
        if (!$token || !$phoneNumberId) {
            // If API not configured, simulate success for testing
            Log::warning('WhatsApp API not configured. Message would be sent to: ' . $phoneNumber);
            return [
                'success' => true,
                'message_id' => 'test_' . uniqid(),
                'note' => 'API not configured - simulated success'
            ];
        }
        
        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $phoneNumber,
                    'type' => 'text',
                    'text' => [
                        'body' => $message
                    ]
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['messages'][0]['id'] ?? null
                ];
            }
            
            Log::error('WhatsApp API Error:', $response->json());
            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Unknown error'
            ];
            
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Format phone number to WhatsApp format
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading zeros
        $phone = ltrim($phone, '0');
        
        // If doesn't start with country code, assume it's missing
        // You may want to add default country code logic here
        
        return $phone;
    }
    
    /**
     * Update .env file value
     */
    protected function updateEnvValue($key, $value)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            if (strpos($content, $key . '=') !== false) {
                $content = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $content
                );
            } else {
                $content .= "\n{$key}={$value}";
            }
            
            file_put_contents($path, $content);
        }
    }
}
