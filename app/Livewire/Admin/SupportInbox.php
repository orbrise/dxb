<?php

namespace App\Livewire\Admin;

use App\Events\MessageStatusUpdated;
use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class SupportInbox extends Component
{
    use WithFileUploads;

    public ?int $selectedConversationId = null;
    public ?object $selectedCustomer = null;
    public string $reply = '';
    public string $searchTerm = '';
    public array $conversationMessages = [];

    public $attachment = null;
    public $voiceNote = null;
    public int $voiceDuration = 0;

    protected $rules = [
        'reply' => 'nullable|min:1|max:2000',
    ];

    public function getListeners(): array
    {
        return [
            'echo-private:support-inbox,NewChatMessage' => 'handleNewMessage',
            'refresh-inbox' => 'refreshInbox',
        ];
    }

    public function mount(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);
    }

    public function handleNewMessage($event): void
    {
        if ($this->selectedConversationId
            && isset($event['message']['conversation_id'])
            && (int)$event['message']['conversation_id'] === (int)$this->selectedConversationId) {
            $this->loadMessages();
            $this->markAsRead();
            $this->dispatch('message-received');
        }
        $this->dispatch('$refresh');
    }

    public function refreshInbox(): void
    {
        if ($this->selectedConversationId) {
            $this->loadMessages();
        }
    }

    /**
     * All support conversations, most recently active first.
     */
    public function getConversations()
    {
        $conversations = Conversation::support()
            ->with(['userOne', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        return $conversations
            ->map(function ($conv) {
                $customer = $conv->userOne;
                return [
                    'id' => $conv->id,
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer?->name ?? $customer?->email ?? 'Unknown',
                    'customer_email' => $customer?->email ?? '',
                    'customer_avatar' => $customer?->avatar ?? null,
                    'last_message' => $conv->latestMessage?->message ?? '',
                    'last_message_at' => $conv->last_message_at,
                    'unread_count' => Message::where('conversation_id', $conv->id)
                        ->where('sender_id', $customer?->id)
                        ->where(function ($q) {
                            $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
                        })
                        ->count(),
                ];
            })
            ->when($this->searchTerm !== '', function ($items) {
                $s = strtolower($this->searchTerm);
                return $items->filter(fn ($c) =>
                    str_contains(strtolower($c['customer_name']), $s) ||
                    str_contains(strtolower($c['customer_email']), $s)
                );
            });
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = Conversation::support()->find($conversationId);
        if (!$conversation) return;

        $this->selectedConversationId = $conversationId;
        $this->selectedCustomer = $conversation->userOne;
        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadMessages(): void
    {
        if (!$this->selectedConversationId) {
            $this->conversationMessages = [];
            return;
        }

        $adminId = auth()->id();
        $messages = Message::where('conversation_id', $this->selectedConversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $this->conversationMessages = $messages->map(function ($msg) use ($adminId) {
            $isAdmin = $msg->sender && ($msg->sender->is_admin ?? false);
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender_name' => $isAdmin
                    ? ('Support · ' . ($msg->sender->name ?? $msg->sender->email))
                    : ($msg->sender?->name ?? $msg->sender?->email ?? 'Customer'),
                'is_mine' => $isAdmin,
                'is_me' => $msg->sender_id === $adminId,
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

    public function markAsRead(): void
    {
        if (!$this->selectedConversationId) return;

        $conversation = Conversation::find($this->selectedConversationId);
        if (!$conversation) return;

        // Mark customer's messages as read from admin side.
        $customerId = $conversation->user_one_id;

        $toUpdate = Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', $customerId)
            ->where(function ($q) {
                $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
            })
            ->get();

        if ($toUpdate->isEmpty()) return;

        Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', $customerId)
            ->where(function ($q) {
                $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
            })
            ->update(['status' => 'read']);

        try {
            broadcast(new MessageStatusUpdated(
                $this->selectedConversationId,
                'read',
                $customerId
            ))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Support inbox: mark-as-read broadcast failed: ' . $e->getMessage());
        }
    }

    public function sendReply(): void
    {
        $this->validate([
            'reply' => 'nullable|min:1|max:2000',
            'attachment' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,mp4,mov,webm',
            'voiceNote' => 'nullable|file|max:20480',
        ]);

        if (empty(trim((string)$this->reply)) && !$this->attachment && !$this->voiceNote) {
            return;
        }

        $conversation = $this->selectedConversationId
            ? Conversation::support()->find($this->selectedConversationId)
            : null;

        if (!$conversation) {
            session()->flash('error', 'Select a support conversation first.');
            return;
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            // messages.message is NOT NULL — use '' for attachment-only rows.
            'message' => $this->reply ?: '',
            'status' => 'sent',
        ];

        if ($this->voiceNote) {
            $data = array_merge($data, $this->storeAttachment($this->voiceNote, $conversation->id, 'audio'), [
                'attachment_duration' => (int) $this->voiceDuration ?: null,
            ]);
        } elseif ($this->attachment) {
            $type = $this->detectAttachmentType($this->attachment->getMimeType());
            $data = array_merge($data, $this->storeAttachment($this->attachment, $conversation->id, $type));
        }

        $message = Message::create($data);

        $conversation->update(['last_message_at' => now()]);

        try {
            broadcast(new NewChatMessage($message, $conversation->user_one_id))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Support inbox: broadcast failed: ' . $e->getMessage());
        }

        $this->reply = '';
        $this->attachment = null;
        $this->voiceNote = null;
        $this->voiceDuration = 0;
        $this->loadMessages();
        $this->dispatch('message-received');
    }

    protected function storeAttachment($upload, int $conversationId, string $type): array
    {
        $ext = $upload->getClientOriginalExtension() ?: $upload->extension() ?: 'bin';
        $filename = Str::random(24) . '.' . $ext;
        $absDir = storage_path('app/public/' . "chat-media/{$conversationId}");
        if (!is_dir($absDir)) {
            @mkdir($absDir, 0755, true);
        }
        // Raw copy — see Chat::storeAttachment for the Flysystem-on-Windows
        // "Path cannot be empty" rationale.
        $sourcePath = $upload->getRealPath() ?: $upload->getPathname();
        copy($sourcePath, $absDir . DIRECTORY_SEPARATOR . $filename);

        return [
            'attachment_path' => "chat-media/{$conversationId}/{$filename}",
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

    public function render()
    {
        $conversations = $this->getConversations();
        return view('livewire.admin.support-inbox', [
            'conversations' => $conversations,
        ])->layout('admin.layout.master-slot');
    }
}
