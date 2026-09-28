<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Call;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Status;
use App\Models\StatusView;
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

    /**
     * Which sidebar view is active — 'chats' shows the conversation list,
     * 'calls' shows the call-history list. Toggled by the bottom nav icons
     * without a full page navigation so open calls / WebRTC state survive.
     */
    public $activeTab = 'chats';

    /**
     * New-chat overlay state. When true, the sidebar shows a full-height
     * user directory instead of the conversation list. Users can search
     * everyone in the DB and start a new conversation with one click.
     */
    public $newChatOpen = false;
    public $newChatSearch = '';

    /**
     * Status feature state. $selectedStatusId drives the right-panel
     * viewer; $textStatusOpen toggles the text composer overlay;
     * $myStatusListOpen expands the sidebar into the "My statuses"
     * per-post list (thumbnails + view counts).
     */
    public $selectedStatusId = null;
    public $textStatusOpen = false;
    public $textStatusContent = '';
    public $textStatusBg = '#075E54';
    public $myStatusListOpen = false;

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

        // Hydrate chat prefs from the current user's row.
        $u = auth()->user();
        $this->prefShowUnanswered      = (bool) ($u->pref_show_unanswered ?? false);
        $this->prefDisappearingDefault = $u->pref_disappearing_default ?? 'never';

        if ($userId && $userId != auth()->id()) {
            $this->startConversation($userId);
        }
    }

    /**
     * Save the "Unanswered" toggle to users.pref_show_unanswered.
     * Called from the Chats settings pane via wire:click.
     */
    public function togglePrefShowUnanswered(): void
    {
        if (!auth()->check()) return;
        $this->prefShowUnanswered = !$this->prefShowUnanswered;
        auth()->user()->update(['pref_show_unanswered' => $this->prefShowUnanswered]);
    }

    /**
     * Save the default disappearing-messages TTL to
     * users.pref_disappearing_default. Called from the Chats settings pane.
     */
    public function setPrefDisappearing(string $ttl): void
    {
        if (!in_array($ttl, ['never', '24h', '1w', '1m'], true)) return;
        if (!auth()->check()) return;
        $this->prefDisappearingDefault = $ttl;
        auth()->user()->update(['pref_disappearing_default' => $ttl]);
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

        // Users who have any active status in the last 24 h — feeds the
        // green/grey ring around avatars on the Chats list, matching the
        // WhatsApp "someone posted, tap to view" affordance.
        $cutoff = now()->subDay();
        $statusUserIds = Status::where('created_at', '>', $cutoff)
            ->where('user_id', '!=', $userId)
            ->distinct()
            ->pluck('user_id');
        // Users whose latest active status the current user hasn't seen.
        $unseenStatusUserIds = Status::where('created_at', '>', $cutoff)
            ->where('user_id', '!=', $userId)
            ->whereDoesntHave('views', fn ($q) => $q->where('viewer_id', $userId))
            ->distinct()
            ->pluck('user_id')
            ->flip();
        $statusUserIds = $statusUserIds->flip();

        $conversations = Conversation::forUser($userId)
            ->with(['userOne', 'userTwo', 'latestMessage'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conv) use ($userId, $missedByCaller, $statusUserIds, $unseenStatusUserIds) {
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
                        'has_status' => false,
                        'status_unseen' => false,
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
                    'has_status' => $statusUserIds->has($otherId),
                    'status_unseen' => $unseenStatusUserIds->has($otherId),
                    'other_user_avatar' => $otherUser->avatar ?? null,
                    // Pre-computed avatar URL — routes through the
                    // /u/{id}/avatar controller so both symlink-less hosts
                    // and Google-OAuth full URLs render correctly.
                    'other_user_avatar_url' => $otherUser ? user_avatar_url($otherUser) : null,
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

        // "Show all unanswered chats" pref (Chats settings). An unanswered
        // thread = last message was from the other party AND I haven't
        // replied yet, i.e. the row is unread OR my last message pre-dates
        // the last inbound one. Cheap proxy: latest message isn't mine.
        if ($this->prefShowUnanswered) {
            $conversations = $conversations->filter(fn ($c) => !$c['is_support'] && !$c['is_mine']);
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
            ->active() // skip messages whose expires_at has passed
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
                'expires_at' => $msg->expires_at?->toISOString(),
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
            // Disappearing messages: honor the sender's own default TTL.
            // Null when 'never' — those messages stick around indefinitely.
            'expires_at' => Message::ttlToExpiry(auth()->user()->pref_disappearing_default ?? 'never'),
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

    /**
     * Open a conversation with a peer from a call-history row and jump
     * back to the Chats tab in the same click.
     */
    public function openChatFromCall(int $peerId): void
    {
        $this->activeTab = 'chats';
        $this->startConversation($peerId);
    }

    /**
     * Show / hide the New-chat overlay (user directory).
     */
    public function openNewChat(): void
    {
        $this->newChatOpen = true;
        $this->newChatSearch = '';
    }

    public function closeNewChat(): void
    {
        $this->newChatOpen = false;
        $this->newChatSearch = '';
    }

    /**
     * Called when a user is picked from the New-chat directory — opens
     * (or creates) the conversation and closes the overlay.
     */
    public function startNewChatWith(int $userId): void
    {
        $this->activeTab = 'chats';
        $this->newChatOpen = false;
        $this->newChatSearch = '';
        $this->startConversation($userId);
    }

    /**
     * Full user directory for the New-chat overlay.
     *
     * Privacy rules:
     *  - Public accounts appear in browsing + all searches.
     *  - Private accounts NEVER appear in the unfiltered browse list, and
     *    only appear on an EXACT @username match — so someone can still
     *    reach a private user if they know the handle.
     *
     * Search matches on name, email, or username. A leading '@' is stripped
     * (people often type it in). Returns up to 100 rows, alphabetical.
     */
    public function getDirectoryUsers()
    {
        if (!auth()->check()) return collect();

        $rawTerm = trim((string) $this->newChatSearch);
        $needle  = ltrim($rawTerm, '@');
        $isExactHandleQuery = $rawTerm !== '' && str_starts_with($rawTerm, '@');

        $q = User::where('id', '!=', auth()->id());

        if ($needle !== '') {
            $q->where(function ($sub) use ($needle, $isExactHandleQuery) {
                if ($isExactHandleQuery) {
                    // @-prefixed search — exact username match wins so that
                    // private accounts are reachable by handle.
                    $sub->where('username', $needle);
                } else {
                    $sub->where('name', 'like', "%{$needle}%")
                        ->orWhere('email', 'like', "%{$needle}%")
                        ->orWhere('username', 'like', "%{$needle}%");
                }
            });
        }

        // Private accounts are hidden from every browse / substring search.
        // The only way to find them is an exact @username match (handled above).
        if (!$isExactHandleQuery) {
            $q->where(function ($sub) {
                $sub->where('is_private', 0)->orWhereNull('is_private');
            });
        }

        return $q->orderBy('name')->limit(100)->get();
    }

    /**
     * Settings sub-section state — which pane the right panel renders when
     * the Settings tab is active. Null = show the big "Settings" splash.
     * Values: 'account' | 'chats' | 'notifications' | 'video'.
     */
    public $settingsSection = null;

    // Account form fields, hydrated in mount() and edited by the pane form.
    public $accountUsername    = '';
    public $accountPhone       = '';
    public $accountCountryCode = '';
    public $accountIsPrivate   = false;
    public $accountSaved       = false;

    // Chat preferences, server-persisted so they affect list queries and
    // the disappearing-message TTL applied to outgoing messages. Hydrated
    // in mount() from the current user's row.
    public $prefShowUnanswered    = false;
    public $prefDisappearingDefault = 'never'; // never|24h|1w|1m

    /**
     * Switch between the Chats / Calls / Status / Gallery / Settings sidebar
     * views without a page navigation, so WebRTC state on window.__rtc
     * survives.
     */
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['chats', 'calls', 'status', 'gallery', 'settings'], true) ? $tab : 'chats';

        // Match CallLog::mount() — surfacing the calls view acknowledges
        // any missed calls, so the red badge on the phone icon clears.
        if ($this->activeTab === 'calls' && auth()->check()) {
            Call::where('callee_id', auth()->id())
                ->where('status', 'missed')
                ->whereNull('seen_at')
                ->update(['seen_at' => now()]);
        }

        // Reset transient Status UI state when switching away.
        if ($this->activeTab !== 'status') {
            $this->selectedStatusId = null;
            $this->textStatusOpen = false;
            $this->myStatusListOpen = false;
        }
        // Same for the Settings sub-section pane.
        if ($this->activeTab !== 'settings') {
            $this->settingsSection = null;
        }
    }

    public function setSettingsSection(?string $section): void
    {
        $this->settingsSection = in_array($section, ['account', 'chats', 'notifications', 'video'], true) ? $section : null;
        $this->accountSaved = false;

        // When opening the Account pane, hydrate the form with current values.
        if ($this->settingsSection === 'account' && auth()->check()) {
            $u = auth()->user();
            $this->accountUsername    = $u->username ?? '';
            $this->accountPhone       = $u->phone ?? '';
            $this->accountCountryCode = $u->country_code ?? '92';
            $this->accountIsPrivate   = (bool) ($u->is_private ?? false);
        }
    }

    /**
     * Persist Account pane changes. Username must be unique; phone/country
     * are optional; is_private is a plain boolean.
     */
    public function saveAccountSettings(): void
    {
        if (!auth()->check()) return;

        $rules = [
            'accountUsername'    => 'nullable|string|min:3|max:60|regex:/^[a-z0-9_.-]+$/i|unique:users,username,' . auth()->id(),
            'accountPhone'       => 'nullable|string|max:20',
            'accountCountryCode' => 'nullable|string|max:5',
            'accountIsPrivate'   => 'boolean',
        ];
        $this->validate($rules, [
            'accountUsername.regex'  => 'Username can only use letters, numbers, dots, underscores and hyphens.',
            'accountUsername.unique' => 'That username is already taken.',
        ]);

        auth()->user()->update([
            'username'     => $this->accountUsername ?: null,
            'phone'        => $this->accountPhone ?: null,
            'country_code' => $this->accountCountryCode ?: null,
            'is_private'   => (bool) $this->accountIsPrivate,
        ]);

        $this->accountSaved = true;
    }

    /**
     * Toggle the "My statuses" per-post list. Opened from the "My status"
     * sidebar card when the user has active statuses.
     */
    public function openMyStatusList(): void
    {
        $this->myStatusListOpen = true;
        $this->selectedStatusId = null;
    }

    public function closeMyStatusList(): void
    {
        $this->myStatusListOpen = false;
    }

    /**
     * Toggle / open the text-status composer.
     */
    public function openTextStatus(): void
    {
        $this->textStatusOpen = true;
        $this->textStatusContent = '';
        $this->textStatusBg = '#075E54';
    }

    public function closeTextStatus(): void
    {
        $this->textStatusOpen = false;
        $this->textStatusContent = '';
    }

    /**
     * Persist a text status. Called from the composer overlay's "Post" button.
     */
    public function postTextStatus(): void
    {
        if (!auth()->check()) return;
        $content = trim((string) $this->textStatusContent);
        if ($content === '') return;

        Status::create([
            'user_id'          => auth()->id(),
            'type'             => 'text',
            'content'          => mb_substr($content, 0, 500),
            'background_color' => $this->textStatusBg ?: '#075E54',
            'text_color'       => '#ffffff',
        ]);

        $this->closeTextStatus();
    }

    /**
     * Open the viewer for a specific status and record the view.
     */
    public function viewStatus(int $statusId): void
    {
        $status = Status::find($statusId);
        if (!$status || $status->isExpired()) return;

        $this->selectedStatusId = $statusId;

        if ($status->user_id !== auth()->id()) {
            StatusView::firstOrCreate(
                ['status_id' => $statusId, 'viewer_id' => auth()->id()],
                ['viewed_at' => now()]
            );
        }
    }

    public function closeStatusViewer(): void
    {
        $this->selectedStatusId = null;
    }

    /**
     * Delete a status the current user owns (removes the file too).
     */
    public function deleteStatus(int $statusId): void
    {
        $status = Status::where('user_id', auth()->id())->find($statusId);
        if (!$status) return;

        if ($status->media_path) {
            $abs = storage_path('app/public/' . $status->media_path);
            if (is_file($abs)) @unlink($abs);
        }
        $status->delete();

        if ($this->selectedStatusId === $statusId) {
            $this->selectedStatusId = null;
        }
    }

    /**
     * Data for the Status tab: caller's own statuses + everyone else's
     * grouped by user, ordered most-recent-first. Only active (last 24h).
     */
    public function getStatusFeed(): array
    {
        if (!auth()->check()) {
            return ['mine' => collect(), 'others' => collect()];
        }

        $userId = auth()->id();
        $cutoff = now()->subDay();

        $mine = Status::where('user_id', $userId)
            ->where('created_at', '>', $cutoff)
            ->withCount('views')
            ->orderByDesc('created_at')
            ->get();

        $others = Status::with('user')
            ->where('user_id', '!=', $userId)
            ->where('created_at', '>', $cutoff)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) use ($userId) {
                $first = $group->first();
                $unseen = $group->filter(fn ($s) => !$s->viewedBy($userId))->count();
                return [
                    'user_id'    => $first->user_id,
                    'user_name'  => $first->user->name ?? $first->user->email ?? 'Unknown',
                    'user_avatar'=> $first->user->avatar ?? null,
                    'user_avatar_url' => $first->user ? user_avatar_url($first->user) : null,
                    'latest_at'  => $first->created_at,
                    'count'      => $group->count(),
                    'unseen'     => $unseen,
                    'latest_id'  => $first->id,
                ];
            })
            ->sortByDesc('latest_at')
            ->values();

        return ['mine' => $mine, 'others' => $others];
    }

    /**
     * Selected status + its viewers, for the right-panel viewer.
     */
    public function getSelectedStatus(): ?array
    {
        if (!$this->selectedStatusId) return null;

        $status = Status::with('user')->find($this->selectedStatusId);
        if (!$status || $status->isExpired()) return null;

        $isMine = $status->user_id === auth()->id();
        $viewers = $isMine
            ? StatusView::with('viewer')
                ->where('status_id', $status->id)
                ->orderByDesc('viewed_at')
                ->get()
            : collect();

        return [
            'id'          => $status->id,
            'user_id'     => $status->user_id,
            'user_name'   => $status->user->name ?? $status->user->email ?? 'Unknown',
            'user_avatar' => $status->user->avatar ?? null,
            'user_avatar_url' => $status->user ? user_avatar_url($status->user) : null,
            'type'        => $status->type,
            'media_path'  => $status->media_path,
            'content'     => $status->content,
            'background_color' => $status->background_color,
            'text_color'  => $status->text_color,
            'created_at'  => $status->created_at,
            'is_mine'     => $isMine,
            'viewers'     => $viewers,
        ];
    }

    /**
     * Open a conversation from a Gallery thumbnail — same as
     * selectConversation() but dispatches a client event so JS can
     * scroll the specific message into view once the thread renders.
     */
    public function openChatFromGallery(int $conversationId, int $messageId): void
    {
        $this->selectConversation($conversationId);
        $this->dispatch('scroll-to-message', messageId: $messageId);
    }

    /**
     * All photo / video attachments the current user can see, across
     * every conversation they're a participant of. Ordered newest first.
     * Feeds the Gallery tab thumbnail grid.
     */
    public function getGalleryMedia()
    {
        if (!auth()->check()) return collect();

        $userId = auth()->id();

        $conversationIds = Conversation::forUser($userId)->pluck('id');

        return Message::whereIn('conversation_id', $conversationIds)
            ->active()
            ->whereIn('attachment_type', ['image', 'video'])
            ->whereNotNull('attachment_path')
            ->orderByDesc('created_at')
            ->limit(200)
            ->get()
            ->map(function ($m) {
                return [
                    'id'              => $m->id,
                    'conversation_id' => $m->conversation_id,
                    'type'            => $m->attachment_type,
                    'url'             => route('chat.media.serve', $m->id),
                    'created_at'      => $m->created_at,
                ];
            });
    }

    /**
     * Recent call history for the Calls tab. Mirrors CallLog::render()
     * so the two lists stay visually identical.
     */
    public function getCallHistory()
    {
        if (!auth()->check()) return collect();

        $userId = auth()->id();

        return Call::with(['caller', 'callee'])
            ->where(function ($q) use ($userId) {
                $q->where('caller_id', $userId)->orWhere('callee_id', $userId);
            })
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($call) use ($userId) {
                $isOutgoing = $call->caller_id === $userId;
                $peer = $isOutgoing ? $call->callee : $call->caller;
                return [
                    'id' => $call->id,
                    'is_outgoing' => $isOutgoing,
                    'type' => $call->type,
                    'status' => $call->status,
                    'peer_id' => $peer?->id,
                    'peer_name' => $peer?->name ?? $peer?->email ?? 'Unknown',
                    'peer_avatar' => $peer?->avatar ?? null,
                    'peer_avatar_url' => $peer ? user_avatar_url($peer) : null,
                    'started_at' => $call->started_at ?? $call->created_at,
                    'duration' => $call->duration,
                ];
            });
    }

    public function render()
    {
        $conversations = $this->getConversations();
        $searchResults = $this->searchUsers();
        $callHistory = $this->activeTab === 'calls' ? $this->getCallHistory() : collect();
        $directoryUsers = $this->newChatOpen ? $this->getDirectoryUsers() : collect();
        $statusFeed = $this->activeTab === 'status' ? $this->getStatusFeed() : ['mine' => collect(), 'others' => collect()];
        $selectedStatus = $this->activeTab === 'status' ? $this->getSelectedStatus() : null;
        $galleryMedia = $this->activeTab === 'gallery' ? $this->getGalleryMedia() : collect();

        $totalConversations = Conversation::forUser(auth()->id())->count();
        $unreadCount = 0;
        foreach ($conversations as $conv) {
            $unreadCount += $conv['unread_count'];
        }

        return view('livewire.chat', compact(
            'conversations',
            'searchResults',
            'totalConversations',
            'unreadCount',
            'callHistory',
            'directoryUsers',
            'statusFeed',
            'selectedStatus',
            'galleryMedia'
        ))->layout('components.layouts.app-evoory');
    }
}
