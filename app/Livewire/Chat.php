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
use App\Support\ChatBadges;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
     * Reply-to state. Set via startReply() from the per-message menu; cleared
     * after send or on cancel. Persisted to messages.reply_to_id so the quote
     * block renders on load.
     */
    public $replyingToMessageId = null;
    public $replyingToPreview = null; // ['sender_name' => ..., 'excerpt' => ...]

    /**
     * Forward state. Opens a modal picker that lists the user's other
     * conversations; selecting one calls forwardTo() to clone the message.
     */
    public $forwardingMessageId = null;
    public $forwardPickerOpen = false;

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
     * Begin a reply. Snapshots the parent's sender name + text excerpt into
     * $replyingToPreview so the input bar can render a quote strip without
     * re-querying.
     */
    public function startReply(int $messageId): void
    {
        $parent = Message::with('sender')->find($messageId);
        if (!$parent || $parent->conversation_id !== (int) $this->selectedConversationId) return;

        $isMine = $parent->sender_id === auth()->id();
        $name = $isMine ? 'You' : ($parent->sender?->name ?? $parent->sender?->email ?? 'Unknown');

        $excerpt = trim((string) $parent->message);
        if ($excerpt === '') {
            $excerpt = match ($parent->attachment_type) {
                'image' => 'Photo',
                'audio' => 'Voice message',
                'video' => 'Video',
                null    => '',
                default => 'Attachment',
            };
        }
        $excerpt = \Illuminate\Support\Str::limit($excerpt, 80);

        $this->replyingToMessageId = $parent->id;
        $this->replyingToPreview = ['sender_name' => $name, 'excerpt' => $excerpt];
    }

    public function cancelReply(): void
    {
        $this->replyingToMessageId = null;
        $this->replyingToPreview = null;
    }

    /**
     * Open the forward picker for a given message. The picker lists the
     * user's other conversations; forwardTo() finishes the clone.
     */
    public function startForward(int $messageId): void
    {
        $msg = Message::find($messageId);
        if (!$msg) return;
        // Must be a participant in the source conv.
        $conv = Conversation::find($msg->conversation_id);
        if (!$conv || !$conv->hasUser(auth()->id())) return;

        $this->forwardingMessageId = $messageId;
        $this->forwardPickerOpen = true;
    }

    public function cancelForward(): void
    {
        $this->forwardingMessageId = null;
        $this->forwardPickerOpen = false;
    }

    /**
     * Forward the currently-selected message into $targetConvId. Creates a
     * fresh Message row with the same body/attachment metadata (attachment
     * file is referenced, not copied — safe because messages are
     * append-only). Clears forward state afterwards.
     */
    public function forwardTo(int $targetConvId): void
    {
        $userId = auth()->id();
        if (!$userId || !$this->forwardingMessageId) return;

        $target = Conversation::find($targetConvId);
        if (!$target || !$target->hasUser($userId)) return;

        // Block guard: refuse to forward into a conv where the peer is blocked.
        if (!$target->is_support) {
            $peerId = $target->getOtherUserId($userId);
            if ($peerId && auth()->user()->isBlockRelationWith($peerId)) {
                session()->flash('error', "Can't forward — this user is blocked.");
                $this->cancelForward();
                return;
            }
        }

        $src = Message::find($this->forwardingMessageId);
        if (!$src) { $this->cancelForward(); return; }

        $new = Message::create([
            'conversation_id'          => $target->id,
            'sender_id'                => $userId,
            'message'                  => $src->message ?: '',
            'status'                   => 'sent',
            'attachment_path'          => $src->attachment_path,
            'attachment_type'          => $src->attachment_type,
            'attachment_mime'          => $src->attachment_mime,
            'attachment_size'          => $src->attachment_size,
            'attachment_duration'      => $src->attachment_duration,
            'attachment_original_name' => $src->attachment_original_name,
            'expires_at'               => Message::ttlToExpiry(auth()->user()->pref_disappearing_default ?? 'never'),
        ]);

        $target->update(['last_message_at' => now()]);

        // Defer broadcast (see note on sendReply above).
        $isSupport = (bool) $target->is_support;
        $peerId = $isSupport ? null : $target->getOtherUserId($userId);
        app()->terminating(function () use ($new, $isSupport, $peerId) {
            try {
                if ($isSupport) {
                    broadcast(new NewChatMessage($new, 0))->toOthers();
                } elseif ($peerId) {
                    broadcast(new NewChatMessage($new, $peerId))->toOthers();
                }
            } catch (\Throwable $e) {
                Log::warning('Forward broadcast failed: ' . $e->getMessage());
            }
        });

        $peerId = $target->is_support ? null : $target->getOtherUserId($userId);
        $this->invalidateChatCache($peerId);

        $this->cancelForward();

        // If the user is viewing the target conv, refresh immediately.
        if ((int) $this->selectedConversationId === $target->id) {
            $this->loadConversationMessages();
        }
    }

    /**
     * Delete a single message. Only the sender can delete their own.
     * Also wipes any attachment file from disk so storage doesn't leak.
     */
    /**
     * Toggle whether the current user has starred a message. Per-user: each
     * user maintains their own starred list. Stored in message_stars pivot.
     * Guard: only lets you star messages in a conversation you're part of.
     */
    public function toggleStar(int $messageId): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        // Ensure caller participates in the message's conversation.
        $msg = Message::where('id', $messageId)
            ->whereHas('conversation', function ($q) use ($userId) {
                $q->where('user_one_id', $userId)->orWhere('user_two_id', $userId);
            })->first();
        if (!$msg) return;

        $existing = DB::table('message_stars')
            ->where('user_id', $userId)
            ->where('message_id', $messageId)
            ->first();

        if ($existing) {
            DB::table('message_stars')->where('id', $existing->id)->delete();
        } else {
            DB::table('message_stars')->insert([
                'user_id'    => $userId,
                'message_id' => $messageId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Refresh thread so bubble star indicator updates immediately.
        if ((int) $this->selectedConversationId === (int) $msg->conversation_id) {
            $this->loadConversationMessages();
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        $msg = Message::where('id', $messageId)
            ->where('sender_id', $userId)
            ->first();
        if (!$msg) return;

        if ($msg->attachment_path) {
            try { Storage::disk('public')->delete($msg->attachment_path); } catch (\Throwable $e) {}
        }

        $convId = $msg->conversation_id;
        $msg->delete();

        // Reload the thread so the deleted message disappears locally.
        if ($this->selectedConversationId === $convId) {
            $this->loadConversationMessages();
        }
    }

    /**
     * Toggle a one-way block on another user. Only this user's view is
     * affected — the other side can still see us (standard WhatsApp
     * behavior). Guards elsewhere (sendReply, startConversation, status,
     * incoming message filter) honor both directions of the relationship.
     */
    public function toggleBlock(int $otherUserId): void
    {
        $userId = auth()->id();
        if (!$userId || $otherUserId === $userId) return;

        $existing = DB::table('user_blocks')
            ->where('blocker_id', $userId)
            ->where('blocked_id', $otherUserId)
            ->first();

        if ($existing) {
            DB::table('user_blocks')->where('id', $existing->id)->delete();
        } else {
            DB::table('user_blocks')->insert([
                'blocker_id' => $userId,
                'blocked_id' => $otherUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Invalidate conversation cache so the sidebar reflects the change
        // and any `is_blocked_*` flags refresh on next render.
        Cache::forget(sprintf(
            'chat.convs.%d.%s.%d',
            $userId,
            md5((string) $this->searchTerm),
            $this->prefShowUnanswered ? 1 : 0
        ));
    }

    /**
     * Toggle the pinned (favorite) state of a conversation. Shared flag on
     * conversations.is_pinned — pinning affects both participants, which
     * matches "mark favorite" semantics the UI shows in the chat header.
     */
    public function togglePin(int $conversationId): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        $conv = Conversation::where('id', $conversationId)
            ->where(function ($q) use ($userId) {
                $q->where('user_one_id', $userId)->orWhere('user_two_id', $userId);
            })->first();
        if (!$conv) return;

        $conv->is_pinned = !$conv->is_pinned;
        $conv->save();

        Cache::forget(sprintf(
            'chat.convs.%d.%s.%d',
            $userId,
            md5((string) $this->searchTerm),
            $this->prefShowUnanswered ? 1 : 0
        ));
    }

    /**
     * Toggle the per-user archived state for a conversation. Per-user, not
     * shared: each participant archives their own copy. Uses the
     * conversation_user_states pivot (one row per user per conv).
     */
    public function toggleArchive(int $conversationId): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        // Guard: only participants can archive their own conversation.
        $isMember = Conversation::where('id', $conversationId)
            ->where(function ($q) use ($userId) {
                $q->where('user_one_id', $userId)->orWhere('user_two_id', $userId);
            })->exists();
        if (!$isMember) return;

        $existing = DB::table('conversation_user_states')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();

        if (!$existing) {
            DB::table('conversation_user_states')->insert([
                'conversation_id' => $conversationId,
                'user_id'         => $userId,
                'archived_at'     => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } else {
            DB::table('conversation_user_states')
                ->where('id', $existing->id)
                ->update([
                    'archived_at' => $existing->archived_at ? null : now(),
                    'updated_at'  => now(),
                ]);
        }

        // Blow the 5s conversation cache for this user so the sidebar
        // reflects the new archive state immediately.
        Cache::forget(sprintf(
            'chat.convs.%d.%s.%d',
            $userId,
            md5((string) $this->searchTerm),
            $this->prefShowUnanswered ? 1 : 0
        ));
    }

    /**
     * Start or open a conversation with a user
     */
    public function startConversation($userId)
    {
        if (auth()->user()->isBlockRelationWith($userId)) {
            session()->flash('error', "Can't start a chat — this user is blocked.");
            return;
        }
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

        // Badge cache powers the global header / mobile nav badges on
        // every non-chat page. Clear it alongside the sidebar caches so
        // read/send/missed-call acknowledgments reflect right away on
        // the user's next page load — otherwise the ~30s TTL would make
        // the badge appear stuck.
        ChatBadges::clear($userId);
        if ($peerId) ChatBadges::clear($peerId);
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

        // Per-user archive flags. One query over the pivot table, keyed by
        // conversation_id, non-null archived_at = archived for THIS user.
        $archivedConvIds = $convIds->isEmpty()
            ? collect()
            : DB::table('conversation_user_states')
                ->whereIn('conversation_id', $convIds)
                ->where('user_id', $userId)
                ->whereNotNull('archived_at')
                ->pluck('conversation_id')
                ->flip();

        // Block relationships keyed by the OTHER user id. Separate lookups
        // for "I blocked them" vs "they blocked me" so the UI can show the
        // right affordance (Unblock vs Blocked-by-them hint).
        $blockedByMe   = DB::table('user_blocks')->where('blocker_id', $userId)->pluck('blocked_id')->flip();
        $blockedByThem = DB::table('user_blocks')->where('blocked_id', $userId)->pluck('blocker_id')->flip();

        $conversations = $conversationRows
            ->map(function ($conv) use ($userId, $missedByCaller, $statusUserIds, $unseenStatusUserIds, $unreadCounts, $archivedConvIds, $blockedByMe, $blockedByThem) {
                if ($conv->is_support) {
                    return [
                        'id' => $conv->id,
                        'is_support' => true,
                        'is_pinned' => true,
                        'is_archived' => false,
                        'is_blocked_by_me' => false,
                        'is_blocked_by_them' => false,
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
                    'is_archived' => $archivedConvIds->has($conv->id),
                    'is_blocked_by_me' => $blockedByMe->has($otherId),
                    'is_blocked_by_them' => $blockedByThem->has($otherId),
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
     * Search users to start new conversation.
     *
     * Prefix-match (LIKE 'term%') instead of substring (LIKE '%term%')
     * so MySQL can use the users(name) / users(email) B-tree index.
     * On a users table with real volume this is the difference between
     * a <50ms indexed lookup and a 5-8 second full table scan — which
     * is what was making the "New chat" search feel broken.
     */
    public function searchUsers()
    {
        if (strlen($this->searchTerm) < 2) {
            return collect();
        }

        $userId = auth()->id();
        // Single query for both block directions (previously two).
        $blockedIds = DB::table('user_blocks')
            ->where(function ($q) use ($userId) {
                $q->where('blocker_id', $userId)->orWhere('blocked_id', $userId);
            })
            ->selectRaw('CASE WHEN blocker_id = ? THEN blocked_id ELSE blocker_id END AS uid', [$userId])
            ->pluck('uid')
            ->unique()
            ->all();

        $term = $this->searchTerm;
        return User::select(['id', 'name', 'email', 'username', 'avatar'])
            ->where('id', '!=', $userId)
            ->when(!empty($blockedIds), fn ($q) => $q->whereNotIn('id', $blockedIds))
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', $term . '%')
                  ->orWhere('email', 'like', $term . '%')
                  ->orWhere('username', 'like', $term . '%');
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

        $this->dispatch('conversation-opened');
    }

    /**
     * Ack any unseen missed calls from the peer we just opened a chat with,
     * so the red badge in the sidebar disappears.
     */
    protected function markMissedCallsSeen(): void
    {
        if ($this->selectedIsSupport || !$this->selectedUser) return;

        $updated = Call::where('callee_id', auth()->id())
            ->where('caller_id', $this->selectedUser->id)
            ->where('status', 'missed')
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);

        if ($updated) ChatBadges::clear(auth()->id());
    }

    public function loadConversationMessages()
    {
        if (!$this->selectedConversationId) {
            $this->conversationMessages = [];
            return;
        }

        // Load only the latest 100 messages (DESC + limit uses the composite
        // index efficiently), then reverse to ASC for display. Long threads
        // with thousands of rows used to ship the entire history on every
        // tap — this caps initial load at ~100 rows regardless of thread age.
        $messages = Message::where('conversation_id', $this->selectedConversationId)
            ->active() // skip messages whose expires_at has passed
            ->with(['sender', 'replyTo.sender'])
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        $userId = auth()->id();
        $isSupport = $this->selectedIsSupport;

        // Batched star lookup — one query for the whole thread instead of
        // per-row EXISTS.
        $starredIds = $messages->isEmpty()
            ? collect()
            : DB::table('message_stars')
                ->whereIn('message_id', $messages->pluck('id'))
                ->where('user_id', $userId)
                ->pluck('message_id')
                ->flip();

        $this->conversationMessages = $messages->map(function ($msg) use ($userId, $isSupport, $starredIds) {
            $isMine = $msg->sender_id === $userId;
            $senderName = $isSupport && !$isMine
                ? 'Support'
                : ($msg->sender?->name ?? $msg->sender?->email ?? 'Unknown');

            // Snapshot of the message this one replies to, if any.
            $replyTo = null;
            if ($msg->replyTo) {
                $r = $msg->replyTo;
                $rIsMine = $r->sender_id === $userId;
                $rName = $isSupport && !$rIsMine
                    ? 'Support'
                    : ($rIsMine ? 'You' : ($r->sender?->name ?? $r->sender?->email ?? 'Unknown'));
                $rText = trim((string) $r->message);
                if ($rText === '') {
                    $rText = match ($r->attachment_type) {
                        'image' => 'Photo',
                        'audio' => 'Voice message',
                        'video' => 'Video',
                        null    => '',
                        default => 'Attachment',
                    };
                }
                $replyTo = [
                    'id'          => $r->id,
                    'sender_name' => $rName,
                    'excerpt'     => \Illuminate\Support\Str::limit($rText, 80),
                ];
            }

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
                'reply_to' => $replyTo,
                'is_starred' => $starredIds->has($msg->id),
            ];
        })->toArray();
    }

    /**
     * Fetch all messages the current user has starred, across every
     * conversation they're part of. Used by the "Starred messages" overlay
     * from the Contact Info panel.
     */
    public function getStarredMessages()
    {
        $userId = auth()->id();
        if (!$userId) return collect();

        $msgIds = DB::table('message_stars')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(200)
            ->pluck('message_id');

        if ($msgIds->isEmpty()) return collect();

        return Message::whereIn('id', $msgIds)
            ->with(['sender', 'conversation'])
            ->active()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($m) use ($userId) {
                $peer = $m->conversation?->getOtherUser($userId);
                return [
                    'id'               => $m->id,
                    'conversation_id'  => $m->conversation_id,
                    'message'          => $m->message,
                    'sender_name'      => $m->sender?->name ?? $m->sender?->email ?? 'Unknown',
                    'is_mine'          => $m->sender_id === $userId,
                    'created_at'       => $m->created_at->toISOString(),
                    'attachment_type'  => $m->attachment_type,
                    'attachment_url'   => $m->attachment_url,
                    'peer_name'        => $peer?->name ?? $peer?->email ?? '',
                ];
            });
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

        // Reading messages drops this user's unread badge — clear it so
        // the mobile nav / header reflects the new count on next page load
        // instead of waiting out the 30s TTL.
        ChatBadges::clear($userId);

        // Defer the broadcasts until AFTER the Livewire response is sent to
        // the browser. Each broadcast() is a blocking HTTP POST to Reverb;
        // doing N of them synchronously was adding ~1-2s per unique sender
        // to every chat click in prod. The receipts arrive a beat later but
        // the user's chat opens instantly.
        $convId = (int) $this->selectedConversationId;
        app()->terminating(function () use ($senderIds, $convId) {
            try {
                foreach ($senderIds as $senderId) {
                    broadcast(new MessageStatusUpdated($convId, 'read', (int) $senderId))->toOthers();
                }
            } catch (\Throwable $e) {
                Log::warning('Broadcast status update failed: ' . $e->getMessage());
            }
        });
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

        // Defer broadcasts: this runs on every poll tick (every ~8 seconds)
        // AND on mount(), so synchronous blocking per sender here was adding
        // multiple seconds to initial page load + every subsequent action.
        $grouped = $rows->groupBy('conversation_id');
        app()->terminating(function () use ($grouped) {
            try {
                foreach ($grouped as $convId => $messages) {
                    foreach ($messages->pluck('sender_id')->filter()->unique() as $senderId) {
                        broadcast(new MessageStatusUpdated((int) $convId, 'delivered', (int) $senderId))->toOthers();
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Broadcast delivered status failed: ' . $e->getMessage());
            }
        });
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

        // Block guard: refuse to send if either side has blocked the other.
        if (!$conversation->is_support) {
            $peerId = $conversation->getOtherUserId(auth()->id());
            if ($peerId && auth()->user()->isBlockRelationWith($peerId)) {
                session()->flash('error', "Can't send — this user is blocked.");
                return;
            }
        }

        $data = [
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => auth()->id(),
            // messages.message is NOT NULL in the schema — use '' for
            // attachment-only rows so the insert doesn't fail.
            'message' => $this->reply ?: '',
            'status' => 'sent',
            'reply_to_id' => $this->replyingToMessageId ?: null,
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

        // Defer broadcast to after-response so sendReply returns instantly.
        $isSupport = (bool) $conversation->is_support;
        $otherUserId = $isSupport ? null : $conversation->getOtherUserId(auth()->id());
        app()->terminating(function () use ($message, $isSupport, $otherUserId) {
            try {
                if ($isSupport) {
                    broadcast(new NewChatMessage($message, 0))->toOthers();
                } elseif ($otherUserId) {
                    broadcast(new NewChatMessage($message, $otherUserId))->toOthers();
                }
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed: ' . $e->getMessage());
            }
        });

        $this->reply = '';
        $this->attachment = null;
        $this->voiceNote = null;
        $this->voiceDuration = 0;
        $this->replyingToMessageId = null;
        $this->replyingToPreview = null;
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

    public function deleteConversation(?int $conversationId = null)
    {
        $convId = $conversationId ?: $this->selectedConversationId;
        if (!$convId) return;

        $conversation = Conversation::find($convId);
        if ($conversation && $conversation->hasUser(auth()->id())) {
            // Never let a user delete their own pinned Support conversation
            // — it's a system-owned thread and must always be present.
            if ($conversation->is_support) {
                session()->flash('error', 'The Support conversation cannot be deleted.');
                return;
            }
            Message::where('conversation_id', $convId)->delete();
            $peerId = $conversation->is_support ? null : $conversation->getOtherUserId(auth()->id());
            $conversation->delete();
            $this->invalidateChatCache($peerId);
        }

        // If we just deleted the open thread, close it. If deleted from the
        // sidebar kebab for a non-open conv, leave current thread alone.
        if ((int) $convId === (int) $this->selectedConversationId) {
            $this->closeConversation();
        }
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

        // Empty-search case: everyone opens the overlay to the same list,
        // so cache it per-user for a minute. First open was previously
        // 1-2s just for an ORDER BY name on the entire users table.
        if ($needle === '') {
            $uid = auth()->id();
            return Cache::remember("chat.directory.public.{$uid}", 60, function () use ($uid) {
                return User::select(['id', 'name', 'email', 'username', 'avatar', 'is_private'])
                    ->where('id', '!=', $uid)
                    ->where(function ($sub) {
                        $sub->where('is_private', 0)->orWhereNull('is_private');
                    })
                    ->orderBy('name')
                    ->limit(100)
                    ->get();
            });
        }

        $q = User::select(['id', 'name', 'email', 'username', 'avatar', 'is_private'])
            ->where('id', '!=', auth()->id());

        $q->where(function ($sub) use ($needle, $isExactHandleQuery) {
            if ($isExactHandleQuery) {
                // @-prefixed search — exact username match wins so that
                // private accounts are reachable by handle.
                $sub->where('username', $needle);
            } else {
                // Prefix match so MySQL can use the users(name) /
                // users(email) index — substring LIKE did a full table
                // scan on every keystroke.
                $sub->where('name', 'like', "{$needle}%")
                    ->orWhere('email', 'like', "{$needle}%")
                    ->orWhere('username', 'like', "{$needle}%");
            }
        });

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
            $updated = Call::where('callee_id', auth()->id())
                ->where('status', 'missed')
                ->whereNull('seen_at')
                ->update(['seen_at' => now()]);
            if ($updated) ChatBadges::clear(auth()->id());
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

        // Hide statuses from users we're in a block relationship with
        // (either direction).
        $blockedIds = DB::table('user_blocks')
            ->where('blocker_id', $userId)->pluck('blocked_id')
            ->merge(DB::table('user_blocks')->where('blocked_id', $userId)->pluck('blocker_id'))
            ->unique()
            ->all();

        // Cap at 200 recent statuses — plenty for a 24 h window and
        // stops the query from scanning the whole table on a busy
        // instance. Without a bound, prod DBs with thousands of daily
        // status posts were spending a full second here on every render.
        $othersRaw = Status::with('user')
            ->where('user_id', '!=', $userId)
            ->whereNotIn('user_id', $blockedIds)
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
        // Conversations always needed — drives the sidebar list on every tab.
        $conversations = $this->getConversations();

        // Everything else is gated by UI state. Previously render() ran
        // searchUsers + getCallHistory + getStatusFeed + getGalleryMedia on
        // EVERY render — including every 3s poll cycle — even though these
        // panels aren't visible unless the matching tab/overlay is active.
        // Sidebar search also surfaces user matches when the user types in
        // the conversations search box, so fire searchUsers whenever
        // searchTerm has content or the New-chat overlay is open.
        $wantSearch = $this->newChatOpen || strlen((string) $this->searchTerm) >= 2;
        $searchResults  = $wantSearch ? $this->searchUsers() : collect();
        $directoryUsers = $this->newChatOpen ? $this->getDirectoryUsers() : collect();

        $callHistory    = $this->activeTab === 'calls'  ? $this->getCallHistory() : collect();
        $galleryMedia   = $this->activeTab === 'gallery' ? $this->getGalleryMedia() : collect();

        // Status feed also powers the "my statuses" strip at the top of
        // the Chats tab, so load it for both 'chats' and 'status'. Keep
        // skipping it on calls/gallery/settings.
        $statusFeed = in_array($this->activeTab, ['chats', 'status'], true)
            ? $this->getStatusFeed()
            : ['mine' => collect(), 'others' => collect()];
        $selectedStatus = $this->activeTab === 'status' ? $this->getSelectedStatus() : null;

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
