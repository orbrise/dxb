<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\NewChatMessage;
use App\Events\MessageStatusUpdated;
use Illuminate\Support\Facades\Log;

class Chat extends Component
{
    public $selectedConversationId = null;
    public $selectedUser = null; // The other user in conversation
    public $reply = '';
    public $searchTerm = '';
    public $conversationMessages = [];

    protected $rules = [
        'reply' => 'required|min:1|max:1000',
    ];

    /**
     * Get the listeners for real-time events
     */
    public function getListeners()
    {
        if (!auth()->check()) {
            return [];
        }
        
        $userId = auth()->id();
        
        return [
            "echo-private:chat.{$userId},NewChatMessage" => 'handleNewMessage',
            'refresh-chat' => 'refreshChat',
        ];
    }

    /**
     * Refresh chat - called when new message received via JS Echo
     */
    public function refreshChat()
    {
        // Mark messages as delivered when chat refreshes
        $this->markMessagesAsDelivered();
        
        // Reload messages if conversation is selected
        if ($this->selectedConversationId) {
            $this->loadConversationMessages();
            $this->markConversationAsRead();
            $this->dispatch('message-received');
        }
    }

    /**
     * Handle incoming real-time message
     */
    public function handleNewMessage($event)
    {
        Log::debug('New chat message received', $event);
        
        // If the message is for the currently selected conversation, add it
        if ($this->selectedConversationId && isset($event['message']['conversation_id'])) {
            if ((int)$event['message']['conversation_id'] === (int)$this->selectedConversationId) {
                // Reload messages for current conversation
                $this->loadConversationMessages();
                
                // Mark as read since user is viewing
                $this->markConversationAsRead();
                
                // Dispatch event to scroll to bottom
                $this->dispatch('message-received');
            }
        }
        
        // Refresh the conversation list
        $this->dispatch('$refresh');
    }

    public function mount($userId = null)
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('sign-in');
        }

        // Mark messages as delivered when chat page loads
        $this->markMessagesAsDelivered();

        // If a userId is passed (from clicking on a profile), start/open that conversation
        if ($userId && $userId != auth()->id()) {
            $this->startConversation($userId);
        }
    }

    /**
     * Start or open a conversation with a user
     */
    public function startConversation($userId)
    {
        $conversation = Conversation::getOrCreate(auth()->id(), $userId);
        $this->selectConversation($conversation->id);
    }

    /**
     * Get all conversations for current user
     */
    public function getConversations()
    {
        $userId = auth()->id();
        
        $conversations = Conversation::forUser($userId)
            ->with(['userOne', 'userTwo', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conv) use ($userId) {
                $otherUser = $conv->getOtherUser($userId);
                return [
                    'id' => $conv->id,
                    'other_user' => $otherUser,
                    'other_user_id' => $conv->getOtherUserId($userId),
                    'other_user_name' => $otherUser->name ?? $otherUser->email ?? 'Unknown',
                    'other_user_email' => $otherUser->email ?? '',
                    'other_user_avatar' => $otherUser->avatar ?? null,
                    'last_message' => $conv->latestMessage?->message ?? '',
                    'last_message_at' => $conv->last_message_at,
                    'unread_count' => $conv->getUnreadCountFor($userId),
                    'is_mine' => $conv->latestMessage?->sender_id === $userId,
                ];
            });

        // Apply search filter
        if ($this->searchTerm) {
            $search = strtolower($this->searchTerm);
            $conversations = $conversations->filter(function ($conv) use ($search) {
                return str_contains(strtolower($conv['other_user_name']), $search) ||
                       str_contains(strtolower($conv['other_user_email']), $search);
            });
        }

        return $conversations;
    }

    /**
     * Search users to start new conversation
     */
    public function searchUsers()
    {
        if (strlen($this->searchTerm) < 2) {
            return collect();
        }

        return User::where('id', '!=', auth()->id())
            ->where(function($q) {
                $q->where('email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->limit(10)
            ->get();
    }

    public function selectConversation($conversationId)
    {
        $conversation = Conversation::find($conversationId);
        
        if (!$conversation || !$conversation->hasUser(auth()->id())) {
            return;
        }

        $this->selectedConversationId = $conversationId;
        $this->selectedUser = $conversation->getOtherUser(auth()->id());
        $this->loadConversationMessages();
        $this->markConversationAsRead();
    }

    public function loadConversationMessages()
    {
        if (!$this->selectedConversationId) {
            $this->conversationMessages = [];
            return;
        }

        $messages = Message::where('conversation_id', $this->selectedConversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->conversationMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender?->name ?? $msg->sender?->email ?? 'Unknown',
                'is_mine' => $msg->sender_id === auth()->id(),
                'status' => $msg->status,
                'created_at' => $msg->created_at->toISOString(),
            ];
        })->toArray();
    }

    public function markConversationAsRead()
    {
        if (!$this->selectedConversationId) return;

        $userId = auth()->id();
        
        // Get messages that need to be marked as read
        $messagesToUpdate = Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', '!=', $userId)
            ->whereIn('status', ['sent', 'delivered', 'unread'])
            ->get();
        
        // Also check for null status
        $nullStatusMessages = Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', '!=', $userId)
            ->whereNull('status')
            ->get();
        
        $allMessages = $messagesToUpdate->merge($nullStatusMessages);
        
        if ($allMessages->isNotEmpty()) {
            // Update status to read
            Message::where('conversation_id', $this->selectedConversationId)
                ->where('sender_id', '!=', $userId)
                ->update(['status' => 'read']);
            
            // Notify each sender that their message was read
            $senderIds = $allMessages->pluck('sender_id')->unique();
            foreach ($senderIds as $senderId) {
                broadcast(new MessageStatusUpdated(
                    $this->selectedConversationId,
                    'read',
                    $senderId
                ))->toOthers();
            }
        }
    }

    /**
     * Mark messages as delivered when user opens chat (but hasn't selected the conversation)
     */
    public function markMessagesAsDelivered()
    {
        $userId = auth()->id();
        
        // Get all conversations for this user
        $conversationIds = Conversation::forUser($userId)->pluck('id');
        
        // Get messages that need to be marked as delivered
        $messagesToUpdate = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $userId)
            ->where('status', 'sent')
            ->get();
        
        if ($messagesToUpdate->isNotEmpty()) {
            // Update status to delivered
            Message::whereIn('conversation_id', $conversationIds)
                ->where('sender_id', '!=', $userId)
                ->where('status', 'sent')
                ->update(['status' => 'delivered']);
            
            // Notify each sender that their message was delivered
            $grouped = $messagesToUpdate->groupBy('conversation_id');
            foreach ($grouped as $convId => $messages) {
                $senderIds = $messages->pluck('sender_id')->unique();
                foreach ($senderIds as $senderId) {
                    broadcast(new MessageStatusUpdated(
                        $convId,
                        'delivered',
                        $senderId
                    ))->toOthers();
                }
            }
        }
    }

    public function sendReply()
    {
        $this->validate();

        if (!$this->selectedConversationId) {
            session()->flash('error', 'No conversation selected.');
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if (!$conversation) {
            session()->flash('error', 'Conversation not found.');
            return;
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => auth()->id(),
            'message' => $this->reply,
            'status' => 'sent',
        ]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        // Broadcast to the other user
        $otherUserId = $conversation->getOtherUserId(auth()->id());
        broadcast(new NewChatMessage($message, $otherUserId))->toOthers();

        $this->reply = '';
        $this->loadConversationMessages();
        
        // Dispatch scroll event
        $this->dispatch('message-received');
    }

    public function closeConversation()
    {
        $this->selectedConversationId = null;
        $this->selectedUser = null;
        $this->conversationMessages = [];
    }

    public function deleteConversation()
    {
        if (!$this->selectedConversationId) return;

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation && $conversation->hasUser(auth()->id())) {
            // Delete all messages in conversation
            Message::where('conversation_id', $this->selectedConversationId)->delete();
            // Delete conversation
            $conversation->delete();
        }

        $this->closeConversation();
        session()->flash('success', 'Conversation deleted.');
    }

    public function render()
    {
        $conversations = $this->getConversations();
        $searchResults = $this->searchUsers();

        // Stats
        $totalConversations = Conversation::forUser(auth()->id())->count();
        $unreadCount = 0;
        foreach ($conversations as $conv) {
            $unreadCount += $conv['unread_count'];
        }

        return view('livewire.chat', compact(
            'conversations',
            'searchResults',
            'totalConversations',
            'unreadCount'
        ))->layout('components.layouts.app');
    }
}
