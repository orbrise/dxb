<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Call;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\NewChatMessage;
use App\Events\MessageStatusUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Chat extends Component
{
    use WithFileUploads;

    public $selectedConversationId = null;
    public $selectedUser = null; // The other user in conversation
    public $selectedIsSupport = false;
    public $reply = '';
    public $searchTerm = '';
    public $conversationMessages = [];

    // File uploads (Livewire temporary upload objects).
    public $attachment = null;   // image / file
    public $voiceNote = null;    // audio blob from MediaRecorder
    public $voiceDuration = 0;   // seconds, sent from JS alongside blob

    protected $rules = [
        'reply' => 'nullable|min:1|max:2000',
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
            "echo-private:chat.{$userId},MessageStatusUpdated" => 'handleStatusUpdate',
            'refresh-chat' => 'refreshChat',
        ];
    }

    /**
     * Handle message status update (delivered/read receipts)
     */
    public function handleStatusUpdate($event)
    {
        if ($this->selectedConversationId) {
            $this->loadConversationMessages();
        }
    }

    /**
     * Refresh chat - called when new message received via JS Echo
     */
    public function refreshChat()
    {
        $this->markMessagesAsDelivered();

        if ($this->selectedConversationId) {
            $this->loadConversationMessages();
            $this->markConversationAsRead();
            // Also acknowledge any missed calls from the currently-open
            // peer — otherwise the "1 missed call" badge in the sidebar
            // keeps showing until the user re-clicks the tile.
            $this->markMissedCallsSeen();
            $this->dispatch('message-received');
        }
    }

    /**
     * Background poll refresh — like refreshChat but WITHOUT the
     * 'message-received' scroll-to-bottom dispatch. Called every ~3s from
     * the client so sidebar badges clear + tick marks update even if the
     * broadcast path is late/dropped. Doesn't hijack the user's scroll
     * position if they're reading history above the fold.
     */
    public function pollRefresh()
    {
        if (!auth()->check()) return;
        $this->markMessagesAsDelivered();
        if ($this->selectedConversationId) {
            $this->loadConversationMessages();
            $this->markConversationAsRead();
        }
    }

    /**
     * Handle incoming real-time message
     */
    public function handleNewMessage($event)
    {
        if ($this->selectedConversationId && isset($event['message']['conversation_id'])) {
            if ((int)$event['message']['conversation_id'] === (int)$this->selectedConversationId) {
                $this->loadConversationMessages();
                $this->markConversationAsRead();
                $this->dispatch('message-received');
            }
        }

        $this->dispatch('$refresh');
    }

    public function mount($userId = null)
    {
        if (!auth()->check()) {
            return redirect()->route('sign-in');
        }

        // Ensure this user has a Support conversation (created lazily, pinned to top).
        Conversation::getOrCreateSupport(auth()->id());

        $this->markMessagesAsDelivered();

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
     * Open the Support conversation for the current user.
     */
    public function openSupport()
    {
        $conversation = Conversation::getOrCreateSupport(auth()->id());
        $this->selectConversation($conversation->id);
    }

    /**
     * Get all conversations for current user (Support pinned first).
     */
    public function getConversations()
    {
        $userId = auth()->id();

        // Callers whose missed calls this user hasn't acknowledged yet.
        // Keyed by caller_id → count. Used to render the "missed call" badge
        // on the corresponding conversation row.
        $missedByCaller = Call::unseenMissedFor($userId)
            ->select('caller_id')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('caller_id')
            ->pluck('cnt', 'caller_id');

        $conversations = Conversation::forUser($userId)
            ->with(['userOne', 'userTwo', 'latestMessage'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conv) use ($userId, $missedByCaller) {
                if ($conv->is_support) {
                    return [
                        'id' => $conv->id,
                        'is_support' => true,
                        'is_pinned' => true,
                        'other_user' => null,
                        'other_user_id' => null,
                        'other_user_name' => 'Support',
                        'other_user_email' => 'We usually reply within an hour',
                        'other_user_avatar' => null,
                        'last_message' => $conv->latestMessage?->message ?? '',
                        'last_message_at' => $conv->last_message_at,
                        'unread_count' => $conv->getUnreadCountFor($userId),
                        'is_mine' => $conv->latestMessage?->sender_id === $userId,
                        'missed_calls' => 0,
                    ];
                }

                $otherUser = $conv->getOtherUser($userId);
                $otherId = $conv->getOtherUserId($userId);
                return [
                    'id' => $conv->id,
                    'is_support' => false,
                    'is_pinned' => (bool) $conv->is_pinned,
                    'other_user' => $otherUser,
                    'other_user_id' => $otherId,
                    'other_user_name' => $otherUser->name ?? $otherUser->email ?? 'Unknown',
                    'other_user_email' => $otherUser->email ?? '',
                    'other_user_avatar' => $otherUser->avatar ?? null,
                    'last_message' => $conv->latestMessage?->message ?? '',
                    'last_message_at' => $conv->last_message_at,
                    'unread_count' => $conv->getUnreadCountFor($userId),
                    'is_mine' => $conv->latestMessage?->sender_id === $userId,
                    'missed_calls' => (int) ($missedByCaller[$otherId] ?? 0),
                ];
            });

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
        $this->selectedIsSupport = (bool) $conversation->is_support;
        $this->selectedUser = $this->selectedIsSupport ? null : $conversation->getOtherUser(auth()->id());
        $this->loadConversationMessages();
        $this->markConversationAsRead();
        $this->markMissedCallsSeen();
    }

    /**
     * Ack any unseen missed calls from the peer we just opened a chat with,
     * so the red badge in the sidebar disappears.
     */
    protected function markMissedCallsSeen(): void
    {
        if ($this->selectedIsSupport || !$this->selectedUser) return;

        Call::where('callee_id', auth()->id())
            ->where('caller_id', $this->selectedUser->id)
            ->where('status', 'missed')
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
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

        $userId = auth()->id();
        $isSupport = $this->selectedIsSupport;

        $this->conversationMessages = $messages->map(function ($msg) use ($userId, $isSupport) {
            $isMine = $msg->sender_id === $userId;
            $senderName = $isSupport && !$isMine
                ? 'Support'
                : ($msg->sender?->name ?? $msg->sender?->email ?? 'Unknown');

            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender_name' => $senderName,
                'is_mine' => $isMine,
                'status' => $msg->status,
                'created_at' => $msg->created_at->toISOString(),
                'attachment_url' => $msg->attachment_url,
                'attachment_type' => $msg->attachment_type,
                'attachment_mime' => $msg->attachment_mime,
                'attachment_size' => $msg->attachment_size,
                'attachment_duration' => $msg->attachment_duration,
                'attachment_original_name' => $msg->attachment_original_name,
            ];
        })->toArray();
    }

    public function markConversationAsRead()
    {
        if (!$this->selectedConversationId) return;

        $userId = auth()->id();

        $messagesToUpdate = Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', '!=', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
            })
            ->get();

        if ($messagesToUpdate->isNotEmpty()) {
            Message::where('conversation_id', $this->selectedConversationId)
                ->where('sender_id', '!=', $userId)
                ->where(function ($q) {
                    $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
                })
                ->update(['status' => 'read']);

            try {
                $senderIds = $messagesToUpdate->pluck('sender_id')->filter()->unique();
                foreach ($senderIds as $senderId) {
                    broadcast(new MessageStatusUpdated(
                        $this->selectedConversationId,
                        'read',
                        $senderId
                    ))->toOthers();
                }
            } catch (\Exception $e) {
                Log::warning('Broadcast status update failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Mark messages as delivered when user opens chat (but hasn't selected the conversation)
     */
    public function markMessagesAsDelivered()
    {
        $userId = auth()->id();

        $conversationIds = Conversation::forUser($userId)->pluck('id');

        $messagesToUpdate = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $userId)
            ->where('status', 'sent')
            ->get();

        if ($messagesToUpdate->isNotEmpty()) {
            Message::whereIn('conversation_id', $conversationIds)
                ->where('sender_id', '!=', $userId)
                ->where('status', 'sent')
                ->update(['status' => 'delivered']);

            try {
                $grouped = $messagesToUpdate->groupBy('conversation_id');
                foreach ($grouped as $convId => $messages) {
                    $senderIds = $messages->pluck('sender_id')->filter()->unique();
                    foreach ($senderIds as $senderId) {
                        broadcast(new MessageStatusUpdated(
                            $convId,
                            'delivered',
                            $senderId
                        ))->toOthers();
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Broadcast delivered status failed: ' . $e->getMessage());
            }
        }
    }

    public function sendReply()
    {
        $this->validate([
            'reply' => 'nullable|min:1|max:2000',
            'attachment' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,mp4,mov,webm',
            'voiceNote' => 'nullable|file|max:20480',
        ]);

        // Must have SOMETHING to send.
        if (empty(trim((string)$this->reply)) && !$this->attachment && !$this->voiceNote) {
            return;
        }

        if (!$this->selectedConversationId) {
            session()->flash('error', 'No conversation selected.');
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        if (!$conversation || !$conversation->hasUser(auth()->id())) {
            session()->flash('error', 'Conversation not found.');
            return;
        }

        $data = [
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => auth()->id(),
            // messages.message is NOT NULL in the schema — use '' for
            // attachment-only rows so the insert doesn't fail.
            'message' => $this->reply ?: '',
            'status' => 'sent',
        ];

        // Voice note takes precedence — it always gets its own message.
        if ($this->voiceNote) {
            $stored = $this->storeAttachment($this->voiceNote, $conversation->id, 'audio');
            $data = array_merge($data, $stored, [
                'attachment_duration' => (int) $this->voiceDuration ?: null,
            ]);
        } elseif ($this->attachment) {
            $type = $this->detectAttachmentType($this->attachment->getMimeType());
            $stored = $this->storeAttachment($this->attachment, $conversation->id, $type);
            $data = array_merge($data, $stored);
        }

        $message = Message::create($data);

        $conversation->update(['last_message_at' => now()]);

        try {
            if ($conversation->is_support) {
                broadcast(new NewChatMessage($message, 0))->toOthers();
            } else {
                $otherUserId = $conversation->getOtherUserId(auth()->id());
                if ($otherUserId) {
                    broadcast(new NewChatMessage($message, $otherUserId))->toOthers();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        $this->reply = '';
        $this->attachment = null;
        $this->voiceNote = null;
        $this->voiceDuration = 0;
        $this->loadConversationMessages();
        $this->dispatch('message-received');
    }

    /**
     * Persist a Livewire temporary upload to the public disk and return
     * the columns needed to attach it to a Message.
     *
     * Uses raw file copy via getRealPath()/getPathname() rather than
     * Storage::putFileAs — the latter blows up with "Path cannot be empty"
     * on some Windows/Laragon setups where the temp-file backing chain
     * confuses Flysystem.
     */
    protected function storeAttachment($upload, int $conversationId, string $type): array
    {
        $ext = $upload->getClientOriginalExtension() ?: $upload->extension() ?: 'bin';
        $filename = Str::random(24) . '.' . $ext;
        $relPath = "chat-media/{$conversationId}/{$filename}";
        $absDir = storage_path('app/public/' . "chat-media/{$conversationId}");
        if (!is_dir($absDir)) {
            @mkdir($absDir, 0755, true);
        }
        $sourcePath = $upload->getRealPath() ?: $upload->getPathname();
        copy($sourcePath, $absDir . DIRECTORY_SEPARATOR . $filename);

        return [
            'attachment_path' => $relPath,
            'attachment_type' => $type,
            'attachment_mime' => $upload->getMimeType(),
            'attachment_size' => $upload->getSize(),
            'attachment_original_name' => $upload->getClientOriginalName(),
        ];
    }

    protected function detectAttachmentType(?string $mime): string
    {
        if (!$mime) return 'file';
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        if (str_starts_with($mime, 'video/')) return 'video';
        return 'file';
    }

    public function closeConversation()
    {
        $this->selectedConversationId = null;
        $this->selectedUser = null;
        $this->selectedIsSupport = false;
        $this->conversationMessages = [];
    }

    public function deleteConversation()
    {
        if (!$this->selectedConversationId) return;

        $conversation = Conversation::find($this->selectedConversationId);
        if ($conversation && $conversation->hasUser(auth()->id())) {
            // Never let a user delete their own pinned Support conversation
            // — it's a system-owned thread and must always be present.
            if ($conversation->is_support) {
                session()->flash('error', 'The Support conversation cannot be deleted.');
                return;
            }
            Message::where('conversation_id', $this->selectedConversationId)->delete();
            $conversation->delete();
        }

        $this->closeConversation();
        session()->flash('success', 'Conversation deleted.');
    }

    public function render()
    {
        $conversations = $this->getConversations();
        $searchResults = $this->searchUsers();

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
        ))->layout('components.layouts.app-evoory');
    }
}
