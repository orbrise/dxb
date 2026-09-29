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
use Illuminate\Support\Facades\Cache;
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

        // A new inbound message means the sidebar last-message preview
        // and unread counts are stale — nuke the caches so the next
        // render shows fresh numbers.
        $this->invalidateChatCache();

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
     * Invalidate the per-user chat caches. Called after any write that
     * would change the sidebar list (send, delete, receive) so users
     * see fresh state immediately instead of waiting for the TTL. Keys
     * mirror the ones the getters use.
     *
     * Both the acting user AND the peer's caches need clearing so the
     * other side's sidebar preview updates in real time via their own
     * subsequent render (usually triggered by the Reverb broadcast).
     */
    protected function invalidateChatCache(?int $peerId = null): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        // Wildcards aren't supported cross-store; delete each key we set.
        // getConversations has a compound key from searchTerm +
        // prefShowUnanswered — nuke both search/no-search + toggle both.
        $prefixes = ['chat.convs', 'chat.statusfeed', 'chat.gallery', 'chat.calls'];
        foreach ($prefixes as $prefix) {
            Cache::forget("{$prefix}.{$userId}");
            if ($peerId) Cache::forget("{$prefix}.{$peerId}");
        }
        // Convs cache has extra dimensions for the two toggles — clear
        // all four combinations for the current user.
        foreach (['0', '1'] as $pref) {
            $hash = md5((string) ($this->searchTerm ?? ''));
            Cache::forget("chat.convs.{$userId}.{$hash}.{$pref}");
            Cache::forget("chat.convs.{$userId}." . md5('') . ".{$pref}");
            if ($peerId) {
                Cache::forget("chat.convs.{$peerId}." . md5('') . ".{$pref}");
            }
        }
    }

    /**
     * Get all conversations for current user (Support pinned first).
     *
     * PROD PERF: wrapped in a 5-second cache keyed on the user ID and
     * their current search / unanswered-filter state. Livewire's render()
     * runs on every action — tapping Settings, switching tabs, posting a
     * message — and re-executing the ~5 queries this method does every
     * time was the biggest single contributor to the 5-6 s prod latency.
     *
     * The cache is invalidated explicitly on writes (sendReply,
     * deleteConversation, selectConversation) via invalidateChatCache().
     * Reverb-delivered incoming messages don't touch the cache, but the
     * 5 s TTL means at worst the sidebar preview lags by that much.
     */
    public function getConversations()
    {
        $userId = auth()->id();

        $cacheKey = sprintf(
            'chat.convs.%d.%s.%d',
            $userId,
            md5((string) $this->searchTerm),
            $this->prefShowUnanswered ? 1 : 0
        );

        return Cache::remember($cacheKey, 5, function () use ($userId) {
            return $this->fetchConversations($userId);
        });
    }

    protected function fetchConversations(int $userId)
    {
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

        // Cap the number of conversations we load — top ~100 by
        // last_message_at is more than enough for any active user, and
        // stops power users' sidebars from grinding the DB every 3 s.
        $conversationRows = Conversation::forUser($userId)
            ->with(['userOne', 'userTwo', 'latestMessage'])
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        // BATCHED unread counts — a single aggregation over the messages
        // table instead of one COUNT query per conversation (was N+1 and
        // dominated tab-switch latency). Also filters expired
        // disappearing messages so the badge count agrees with what the
        // user actually sees inside the thread.
        $convIds = $conversationRows->pluck('id');
        $unreadCounts = $convIds->isEmpty()
            ? collect()
            : Message::whereIn('conversation_id', $convIds)
                ->where('sender_id', '!=', $userId)
                ->active() // hide expired disappearing messages
                ->where(function ($q) {
                    $q->whereNull('status')->orWhereIn('status', ['sent', 'delivered', 'unread']);
                })
                ->selectRaw('conversation_id, COUNT(*) as cnt')
                ->groupBy('conversation_id')
                ->pluck('cnt', 'conversation_id');

        $conversations = $conversationRows
            ->map(function ($conv) use ($userId, $missedByCaller, $statusUserIds, $unseenStatusUserIds, $unreadCounts) {
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
                        'unread_count' => (int) ($unreadCounts[$conv->id] ?? 0),
                        'is_mine' => $conv->latestMessage?->sender_id === $userId,
                        'missed_calls' => 0,
                        'has_status' => false,
                        'status_unseen' => false,
                    ];
                }

                // Reuse the userOne / userTwo eager loads instead of
                // calling $conv->getOtherUser() — that method does a fresh
                // User::find() and was hitting the DB once per conversation
                // (100 extra queries per render, on top of the poll every
                // 3 s). This picks the already-loaded relation.
                $otherId = $conv->user_one_id === $userId
                    ? (int) $conv->user_two_id
                    : (int) $conv->user_one_id;
                $otherUser = $conv->user_one_id === $userId
                    ? $conv->userTwo
                    : $conv->userOne;
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
                    'unread_count' => (int) ($unreadCounts[$conv->id] ?? 0),
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

        // Only pluck the sender_ids we need for the broadcast, not full
        // Message models. Was fetching every unread message row into
        // memory just to read sender_id off it.
        $senderIds = Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', '!=', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
            })
            ->pluck('sender_id')
            ->filter()
            ->unique();

        if ($senderIds->isEmpty()) return;

        Message::where('conversation_id', $this->selectedConversationId)
            ->where('sender_id', '!=', $userId)
            ->where(function ($q) {
                $q->whereIn('status', ['sent', 'delivered', 'unread'])->orWhereNull('status');
            })
            ->update(['status' => 'read']);

        try {
            foreach ($senderIds as $senderId) {
                broadcast(new MessageStatusUpdated(
                    $this->selectedConversationId,
                    'read',
                    (int) $senderId
                ))->toOthers();
            }
        } catch (\Exception $e) {
            Log::warning('Broadcast status update failed: ' . $e->getMessage());
        }
    }

    /**
     * Mark messages as delivered when user opens chat (but hasn't selected the conversation).
     *
     * Runs on every 3-second poll refresh — so this is a hot path. The
     * old implementation did:
     *   forUser->pluck(id)  → get() the messages  → update()  → group + broadcast
     * Which meant a full conversations-of-user query + a full messages
     * fetch even when there was nothing to update. Replaced with a
     * cheap EXISTS probe first; the expensive path only runs when there
     * are actually undelivered messages waiting.
     */
    public function markMessagesAsDelivered()
    {
        $userId = auth()->id();

        // Fast probe — most poll ticks find nothing to do.
        $hasUndelivered = Message::where('sender_id', '!=', $userId)
            ->where('status', 'sent')
            ->whereExists(function ($q) use ($userId) {
                $q->select(\DB::raw(1))
                  ->from('conversations')
                  ->whereColumn('conversations.id', 'messages.conversation_id')
                  ->where(function ($w) use ($userId) {
                      $w->where('conversations.user_one_id', $userId)
                        ->orWhere('conversations.user_two_id', $userId);
                  });
            })
            ->exists();

        if (!$hasUndelivered) return;

        $conversationIds = Conversation::forUser($userId)->pluck('id');

        // Grab only sender_id/conversation_id (not full rows) for the
        // broadcast step, then do the single-shot update.
        $rows = Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $userId)
            ->where('status', 'sent')
            ->get(['conversation_id', 'sender_id']);

        if ($rows->isEmpty()) return;

        Message::whereIn('conversation_id', $conversationIds)
            ->where('sender_id', '!=', $userId)
            ->where('status', 'sent')
            ->update(['status' => 'delivered']);

        try {
            foreach ($rows->groupBy('conversation_id') as $convId => $messages) {
                foreach ($messages->pluck('sender_id')->filter()->unique() as $senderId) {
                    broadcast(new MessageStatusUpdated((int) $convId, 'delivered', (int) $senderId))->toOthers();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Broadcast delivered status failed: ' . $e->getMessage());
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

        // Bust sidebar caches so the last-message preview + unread count
        // are fresh on both sides on the next render.
        $peerId = $conversation->is_support ? null : $conversation->getOtherUserId(auth()->id());
        $this->invalidateChatCache($peerId);

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
        // Nothing needs re-rendering — Alpine reactively removes the
        // mobile-hidden class from the sidebar and hides the chat thread
        // via the data-has-conv CSS selector. Skips ~500 ms of prod work.
        $this->skipRender();
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
            $peerId = $conversation->is_support ? null : $conversation->getOtherUserId(auth()->id());
            $conversation->delete();
            $this->invalidateChatCache($peerId);
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
     *
     * PROD PERF: skipRender() is critical here. Alpine's :data-tab
     * binding already switched the visible sidebar body client-side the
     * instant the user tapped — everything the view would re-render
     * (5 tab bodies + right panel + chat thread) is already in the DOM
     * with correct CSS visibility. Re-executing render() would burn
     * ~200-500 ms of blade template work and ship a huge HTML response
     * for zero visual change. Livewire still propagates the entangled
     * state changes (activeTab etc.) without a render, so Alpine and
     * the CSS attribute selectors stay in sync.
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

        $this->skipRender();
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

        $this->invalidateChatCache();
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
        // Alpine's data-has-status attribute reactively hides the viewer.
        $this->skipRender();
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
        $this->invalidateChatCache();

        if ($this->selectedStatusId === $statusId) {
            $this->selectedStatusId = null;
        }
    }

    /**
     * Data for the Status tab: caller's own statuses + everyone else's
     * grouped by user, ordered most-recent-first. Only active (last 24h).
     *
     * PROD PERF: 10-second cache. Statuses are 24 h ephemeral so a bit of
     * staleness is invisible; the real-time cost was 3+ queries per
     * Livewire action for something that rarely changes.
     */
    public function getStatusFeed(): array
    {
        if (!auth()->check()) {
            return ['mine' => collect(), 'others' => collect()];
        }

        $userId = auth()->id();
        return Cache::remember("chat.statusfeed.{$userId}", 10, function () use ($userId) {
            return $this->fetchStatusFeed($userId);
        });
    }

    protected function fetchStatusFeed(int $userId): array
    {
        $cutoff = now()->subDay();

        $mine = Status::where('user_id', $userId)
            ->where('created_at', '>', $cutoff)
            ->withCount('views')
            ->orderByDesc('created_at')
            ->get();

        // Cap at 200 recent statuses — plenty for a 24 h window and
        // stops the query from scanning the whole table on a busy
        // instance. Without a bound, prod DBs with thousands of daily
        // status posts were spending a full second here on every render.
        $othersRaw = Status::with('user')
            ->where('user_id', '!=', $userId)
            ->where('created_at', '>', $cutoff)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        // BATCHED "already viewed by me" lookup — one query for the whole
        // page's worth of statuses instead of one EXISTS per status. Was
        // an N+1 on every render (including the 3s polling refresh).
        $viewedIds = $othersRaw->isEmpty()
            ? collect()
            : StatusView::whereIn('status_id', $othersRaw->pluck('id'))
                ->where('viewer_id', $userId)
                ->pluck('status_id')
                ->flip();

        $others = $othersRaw
            ->groupBy('user_id')
            ->map(function ($group) use ($userId, $viewedIds) {
                $first = $group->first();
                $unseen = $group->filter(fn ($s) => !$viewedIds->has($s->id))->count();
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
     *
     * PROD PERF: 30-second cache. Media additions are rare relative to
     * text messages, so a longer TTL is safe. Was scanning up to 200
     * rows on every render.
     */
    public function getGalleryMedia()
    {
        if (!auth()->check()) return collect();

        $userId = auth()->id();
        return Cache::remember("chat.gallery.{$userId}", 30, function () use ($userId) {
            return $this->fetchGalleryMedia($userId);
        });
    }

    protected function fetchGalleryMedia(int $userId)
    {
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
     *
     * PROD PERF: 10-second cache. Call rows only appear when a call
     * starts / ends, so a bit of lag on the list is fine — the Reverb
     * CallSignal path drives the ringtone / accept UI in real time.
     */
    public function getCallHistory()
    {
        if (!auth()->check()) return collect();

        $userId = auth()->id();
        return Cache::remember("chat.calls.{$userId}", 10, function () use ($userId) {
            return $this->fetchCallHistory($userId);
        });
    }

    protected function fetchCallHistory(int $userId)
    {
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
        // Load data for EVERY tab on every render — not just the active
        // one. Sidebar bodies are all in the DOM at once and toggled via
        // x-show, so tab switches are pure client-side (zero server
        // round-trip). One-time cost per render is a few extra bounded
        // queries; savings per tab click is a full RTT (200-800ms on prod).
        $conversations = $this->getConversations();
        $searchResults = $this->searchUsers();
        $callHistory = $this->getCallHistory();
        $directoryUsers = $this->newChatOpen ? $this->getDirectoryUsers() : collect();
        $statusFeed = $this->getStatusFeed();
        $selectedStatus = $this->getSelectedStatus();
        $galleryMedia = $this->getGalleryMedia();

        // Use the already-fetched conversations collection instead of
        // firing another Conversation::forUser->count() query per render.
        $totalConversations = $conversations->count();
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
