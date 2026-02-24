<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Message;
use App\Models\UsersProfile;
use App\Events\MessageSent;

class Messages extends Component
{ 
    use WithPagination;

    public $selectedConversation = null; // email of selected conversation
    public $reply = '';
    public $searchTerm = '';
    public $filterStatus = 'all';
    public $conversationMessages = [];

    protected $paginationTheme = 'bootstrap';

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
            "echo-private:messages.{$userId},message.received" => 'handleNewMessage',
            'refresh-messages' => '$refresh',
        ];
    }

    /**
     * Handle incoming real-time message
     */
    public function handleNewMessage($event)
    {
        // If the message is for the currently selected conversation, add it
        if ($this->selectedConversation && isset($event['message']['user_email'])) {
            if ($event['message']['user_email'] === $this->selectedConversation) {
                // Add the new message to the conversation
                $this->conversationMessages[] = $event['message'];
                
                // Dispatch event to scroll to bottom
                $this->dispatch('message-received');
            }
        }
        
        // Refresh the conversation list to show new message indicator
        $this->dispatch('$refresh');
    }

    public function mount()
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('sign-in');
        }

        // Check if user has any profiles
        $hasProfiles = UsersProfile::where('user_id', auth()->id())->exists();
        if (!$hasProfiles) {
            session()->flash('info', 'You need to create a profile first to receive messages.');
        }
    }

    public function getConversations()
    {
        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');
        
        if ($userProfiles->isEmpty()) {
            return collect();
        }

        // Get unique conversations grouped by sender email
        $query = Message::whereIn('profile_id', $userProfiles)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'sent');
            })
            ->selectRaw('user_email, code, phone, MAX(created_at) as last_message_at, 
                         COUNT(*) as message_count,
                         SUM(CASE WHEN status IS NULL OR status = "unread" THEN 1 ELSE 0 END) as unread_count,
                         (SELECT message FROM messages m2 WHERE m2.user_email = messages.user_email AND m2.profile_id IN (' . $userProfiles->implode(',') . ') ORDER BY m2.created_at DESC LIMIT 1) as last_message')
            ->groupBy('user_email', 'code', 'phone')
            ->orderByDesc('last_message_at');

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('user_email', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply status filter
        if ($this->filterStatus === 'unread') {
            $query->havingRaw('unread_count > 0');
        }

        return $query->get();
    }

    public function render()
    {
        // Get all profiles owned by the authenticated user
        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');

        // If no profiles, return empty data
        if ($userProfiles->isEmpty()) {
            return view('livewire.messages', [
                'conversations' => collect(),
                'totalMessages' => 0,
                'unreadCount' => 0,
                'repliedCount' => 0,
                'activeCount' => 0,
                'rejectedCount' => 0,
                'pendingCount' => 0,
                'archivedCount' => 0
            ])->layout('components.layouts.app');
        }

        $conversations = $this->getConversations();

        // Count statistics
        $totalMessages = Message::whereIn('profile_id', $userProfiles)->count();
        $unreadCount = Message::whereIn('profile_id', $userProfiles)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', 'unread');
            })
            ->count();
        $repliedCount = Message::whereIn('profile_id', $userProfiles)
            ->whereNotNull('reply')
            ->count();

        // Get profile dashboard navigation counts
        $activeCount = UsersProfile::where('user_id', auth()->id())
            ->whereNull('archived_at')
            ->where('is_active', 1)
            ->count();
            
        $rejectedCount = UsersProfile::where('user_id', auth()->id())
            ->whereHas('rejectedVerification')
            ->count();
            
        $pendingCount = UsersProfile::where('user_id', auth()->id())
            ->whereNull('archived_at')
            ->where('is_verified', 0)
            ->count();
            
        $archivedCount = UsersProfile::where('user_id', auth()->id())
            ->whereNotNull('archived_at')
            ->count();

        return view('livewire.messages', compact('conversations', 'totalMessages', 'unreadCount', 'repliedCount', 'activeCount', 'rejectedCount', 'pendingCount', 'archivedCount'))
            ->layout('components.layouts.app');
    }

    public function selectConversation($email)
    {
        $this->selectedConversation = $email;
        $this->loadConversationMessages();
    }

    public function loadConversationMessages()
    {
        if (!$this->selectedConversation) {
            $this->conversationMessages = [];
            return;
        }

        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');
        
        // Get all messages from this sender
        $messages = Message::whereIn('profile_id', $userProfiles)
            ->where('user_email', $this->selectedConversation)
            ->with('profile')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark all as read
        Message::whereIn('profile_id', $userProfiles)
            ->where('user_email', $this->selectedConversation)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', 'unread');
            })
            ->update(['status' => 'read']);

        $this->conversationMessages = $messages->toArray();
    }

    public function sendReply()
    {
        $this->validate();

        if (!$this->selectedConversation) {
            session()->flash('error', 'No conversation selected.');
            return;
        }

        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');
        
        // Get the most recent message from this sender to reply to
        $latestMessage = Message::whereIn('profile_id', $userProfiles)
            ->where('user_email', $this->selectedConversation)
            ->where(function($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'sent');
            })
            ->orderByDesc('created_at')
            ->first();

        if ($latestMessage) {
            // Create a new message as our reply
            Message::create([
                'user_email' => $this->selectedConversation,
                'profile_id' => $latestMessage->profile_id,
                'message' => $this->reply,
                'code' => $latestMessage->code,
                'phone' => $latestMessage->phone,
                'status' => 'sent', // Mark as our sent message
                'reply' => null,
            ]);
            
            // Update the latest user message with reply
            $latestMessage->update([
                'reply' => $this->reply,
                'status' => 'replied',
                'replied_at' => now()
            ]);
        }

        $this->reply = '';
        $this->loadConversationMessages();
    }

    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);
        if ($message) {
            $message->delete();
            $this->loadConversationMessages();
            session()->flash('success', 'Message deleted.');
        }
    }

    public function deleteConversation()
    {
        if (!$this->selectedConversation) {
            return;
        }

        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');
        
        Message::whereIn('profile_id', $userProfiles)
            ->where('user_email', $this->selectedConversation)
            ->delete();

        $this->selectedConversation = null;
        $this->conversationMessages = [];
        session()->flash('success', 'Conversation deleted successfully.');
    }

    public function closeConversation()
    {
        $this->selectedConversation = null;
        $this->conversationMessages = [];
        $this->reply = '';
        $this->resetValidation();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
        $this->selectedConversation = null;
        $this->conversationMessages = [];
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
        $this->selectedConversation = null;
        $this->conversationMessages = [];
    }
}
