<?php

namespace App\Livewire\Admin;

use App\Events\MessageStatusUpdated;
use App\Events\NewChatMessage;
use App\Jobs\SendSupportBroadcast;
use App\Models\Call;
use App\Models\City;
use App\Models\Conversation;
use App\Models\Country;
use App\Models\Message;
use App\Models\User;
use App\Models\Setting;
use App\Models\Status;
use App\Models\StatusView;
use App\Models\SupportBot;
use App\Models\SupportQuickReply;
use App\Models\UsersProfile;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\GrokSupportBot;
use App\Services\SupportAutoReply;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class SupportInbox extends Component
{
    use WithFileUploads;

    // Same text-status backgrounds the customer app offers.
    public const STATUS_COLORS = ['#075E54', '#128C7E', '#25D366', '#34B7F1', '#7c3aed', '#ec4899', '#f59e0b', '#0a0a0a'];

    public ?int $selectedConversationId = null;
    public ?object $selectedCustomer = null;
    public string $selectedCustomerCountry = '';
    public string $reply = '';
    public string $searchTerm = '';
    public string $inboxTab = 'chat';
    public string $settingsSection = '';
    public string $mediaType = 'media';
    public ?int $mediaConversationId = null;
    public int $mediaLimit = 60;
    public array $conversationMessages = [];
    public ?int $replyingToMessageId = null;
    public ?array $replyingToPreview = null;
    public bool $showForwardModal = false;
    public ?int $forwardingMessageId = null;
    public string $forwardSearch = '';
    public array $forwardConversationIds = [];

    public $attachment = null;
    public $voiceNote = null;
    public int $voiceDuration = 0;
    public bool $showWalletTopupModal = false;
    public string $walletTopupAmount = '';
    public string $walletTopupNote = '';
    public bool $supportCallsBlocked = false;
    public bool $starredViewOpen = false;
    public string $suggestedReply = '';
    public ?int $suggestedByBotId = null;
    public bool $suggestingReply = false;
    public bool $showBroadcastModal = false;
    public string $broadcastAudience = 'selected';
    public string $broadcastSearch = '';
    public array $broadcastUserIds = [];
    public string $broadcastCountryId = '';
    public array $broadcastCityIds = [];
    public string $broadcastText = '';
    public bool $broadcastAllConfirmed = false;
    public $broadcastImage = null;
    public ?int $selectedStatusId = null;
    public bool $statusComposerOpen = false;
    public string $statusText = '';
    public string $statusBg = '#075E54';
    public string $accountName = '';
    public string $accountPhone = '';
    public string $accountCountryCode = '';
    public bool $accountSaved = false;
    public bool $autoReplyEnabled = false;
    public int $autoReplyDelay = 5;
    public string $autoReplyMessage = '';
    public bool $autoReplySaved = false;
    public bool $showQuickReplyForm = false;
    public ?int $editingQuickReplyId = null;
    public string $quickReplyShortcut = '';
    public string $quickReplyMessage = '';

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
        $this->supportCallsBlocked = (bool) Setting::query()->whereKey(1)->value('support_calls_blocked');
        $this->loadAutoReplySettings();
    }

    protected function loadAutoReplySettings(): void
    {
        $setting = Setting::query()->find(1);
        $this->autoReplyEnabled = (bool) ($setting?->support_auto_reply_enabled ?? false);
        $this->autoReplyDelay = (int) ($setting?->support_auto_reply_delay ?: 5);
        $this->autoReplyMessage = trim((string) $setting?->support_auto_reply_message) ?: SupportAutoReply::DEFAULT_MESSAGE;
    }

    public function toggleAutoReply(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $setting = Setting::query()->find(1);
        if (!$setting) return;

        $setting->support_auto_reply_enabled = !$setting->support_auto_reply_enabled;
        if ($setting->support_auto_reply_enabled) {
            // Replies go out from whoever switched it on; keep a usable message.
            $setting->support_auto_reply_sender_id = auth()->id();
            $setting->support_auto_reply_message = trim((string) $setting->support_auto_reply_message) ?: SupportAutoReply::DEFAULT_MESSAGE;
        }
        $setting->save();

        $this->autoReplyEnabled = (bool) $setting->support_auto_reply_enabled;
        $this->autoReplySaved = false;
    }

    public function saveAutoReply(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->autoReplyMessage = trim($this->autoReplyMessage);
        $this->validate([
            'autoReplyDelay' => ['required', 'integer', Rule::in(SupportAutoReply::DELAYS)],
            'autoReplyMessage' => 'required|string|max:1000',
        ], [], [
            'autoReplyDelay' => 'wait time',
            'autoReplyMessage' => 'message',
        ]);

        $setting = Setting::query()->find(1);
        if (!$setting) return;

        $setting->support_auto_reply_delay = $this->autoReplyDelay;
        $setting->support_auto_reply_message = $this->autoReplyMessage;
        $setting->support_auto_reply_sender_id = auth()->id();
        $setting->save();

        $this->autoReplySaved = true;
    }

    public function openBroadcast(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->resetValidation();
        $this->showBroadcastModal = true;
    }

    public function closeBroadcast(): void
    {
        $this->showBroadcastModal = false;
        $this->broadcastSearch = '';
        $this->broadcastUserIds = [];
        $this->broadcastCountryId = '';
        $this->broadcastCityIds = [];
        $this->broadcastText = '';
        $this->broadcastAllConfirmed = false;
        $this->broadcastImage = null;
        $this->resetValidation();
    }

    public function removeBroadcastImage(): void
    {
        $this->broadcastImage = null;
        $this->resetValidation('broadcastImage');
    }

    public function updatedBroadcastAudience(): void
    {
        $this->broadcastAllConfirmed = false;
        $this->resetValidation('broadcastAllConfirmed');
    }

    public function updatedBroadcastCountryId(): void
    {
        $this->broadcastCityIds = [];
        $this->resetValidation(['broadcastCountryId', 'broadcastCityIds']);
    }

    public function getBroadcastCountries()
    {
        return Country::query()->orderBy('nicename')->get(['id', 'nicename']);
    }

    public function getBroadcastCities()
    {
        $country = $this->broadcastCountryId !== ''
            ? Country::query()->find($this->broadcastCountryId)
            : null;

        if (!$country) return collect();

        return City::query()
            ->where('country', $country->nicename)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getBroadcastUsers()
    {
        $query = $this->eligibleBroadcastUsers();

        if ($this->broadcastSearch !== '') {
            $search = '%' . addcslashes($this->broadcastSearch, '%_\\\\') . '%';
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search);
            });
        }

        return $query->orderBy('name')->limit(100)->get(['id', 'name', 'email']);
    }

    protected function eligibleBroadcastUsers()
    {
        return User::query()->where(function ($query) {
            $query->where('is_admin', false)->orWhereNull('is_admin');
        });
    }

    public function sendBroadcast(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->validate([
            'broadcastAudience' => 'required|in:selected,all,location',
            'broadcastText' => 'nullable|string|max:2000',
            'broadcastImage' => 'nullable|image|max:10240',
            'broadcastUserIds' => 'exclude_unless:broadcastAudience,selected|required|array|min:1',
            'broadcastUserIds.*' => 'integer|distinct|exists:users,id',
            'broadcastAllConfirmed' => 'exclude_unless:broadcastAudience,all|required|accepted',
            'broadcastCountryId' => 'exclude_unless:broadcastAudience,location|required|integer|exists:countries,id',
            'broadcastCityIds' => 'exclude_unless:broadcastAudience,location|required|array|min:1',
            'broadcastCityIds.*' => 'integer|distinct|exists:cities,id',
        ]);

        if (trim((string) $this->broadcastText) === '' && !$this->broadcastImage) {
            $this->addError('broadcastText', 'Write a message or attach an image.');
            return;
        }

        $recipientIds = null;
        $recipients = $this->eligibleBroadcastUsers();

        if ($this->broadcastAudience === 'selected') {
            $recipientIds = array_values(array_unique(array_map('intval', $this->broadcastUserIds)));
            $recipients->whereIn('id', $recipientIds);
        } elseif ($this->broadcastAudience === 'location') {
            $country = Country::query()->find($this->broadcastCountryId);
            if (!$country) {
                $this->addError('broadcastCountryId', 'Select a valid country.');
                return;
            }

            $requestedCityIds = array_values(array_unique(array_map('intval', $this->broadcastCityIds)));
            $validCityIds = City::query()
                ->where('country', $country->nicename)
                ->whereIn('id', $requestedCityIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (count($validCityIds) !== count($requestedCityIds)) {
                $this->addError('broadcastCityIds', 'Choose cities that belong to the selected country.');
                return;
            }

            $recipientIds = UsersProfile::query()
                ->whereIn('city', $validCityIds)
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $recipients->whereIn('id', $recipientIds);
        }

        $recipientCount = $recipients->count();
        if ($recipientCount === 0) {
            $errorField = $this->broadcastAudience === 'location' ? 'broadcastCityIds' : 'broadcastUserIds';
            $this->addError($errorField, 'No eligible users were found for this audience.');
            return;
        }

        if ($recipientIds !== null && $recipientCount !== count($recipientIds)) {
            $this->addError('broadcastUserIds', 'Select only non-admin user accounts.');
            return;
        }

        $attachment = null;
        if ($this->broadcastImage) {
            $extension = $this->broadcastImage->guessExtension() ?: 'jpg';
            $filename = Str::random(32) . '.' . $extension;
            $path = $this->broadcastImage->storePubliclyAs('chat-media/broadcasts', $filename, 'public');
            if (!$path) {
                $this->addError('broadcastImage', 'The image could not be stored. Please try again.');
                return;
            }

            $attachment = [
                'path' => $path,
                'disk' => 'public',
                'type' => 'image',
                'mime' => $this->broadcastImage->getMimeType() ?: 'image/jpeg',
                'size' => $this->broadcastImage->getSize(),
                'name' => $this->broadcastImage->getClientOriginalName(),
            ];
        }

        SendSupportBroadcast::dispatch(
            auth()->id(),
            trim((string) $this->broadcastText),
            $recipientIds,
            $attachment
        );

        session()->flash('broadcast-success', "Broadcast queued for {$recipientCount} users.");
        $this->closeBroadcast();
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

    public function setInboxTab(string $tab): void
    {
        if (in_array($tab, ['chat', 'calls', 'status', 'media', 'settings'], true)) {
            $this->inboxTab = $tab;
            $this->starredViewOpen = false;
            $this->statusComposerOpen = false;
        }
    }

    public function setSettingsSection(string $section): void
    {
        if (!in_array($section, ['', 'account', 'tools', 'quick-replies', 'auto-reply', 'calls', 'blocked'], true)) return;

        $this->inboxTab = 'settings';
        $this->settingsSection = $section;
        $this->starredViewOpen = false;
        $this->accountSaved = false;
        $this->autoReplySaved = false;

        if ($section === 'auto-reply') {
            $this->loadAutoReplySettings();
            $this->resetValidation(['autoReplyDelay', 'autoReplyMessage']);
        }

        if ($section === 'account') {
            $admin = auth()->user();
            $this->accountName = (string) ($admin->name ?? '');
            $this->accountPhone = (string) ($admin->phone ?? '');
            $this->accountCountryCode = (string) ($admin->country_code ?? '');
            $this->resetValidation(['accountName', 'accountPhone', 'accountCountryCode']);
        }
    }

    /**
     * Settings → Account. The photo is uploaded separately through the
     * shared /settings/avatar endpoint (same one my-chat uses).
     */
    public function saveAccountSettings(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->accountName = trim($this->accountName);
        $this->accountPhone = trim($this->accountPhone);
        $this->accountCountryCode = ltrim(trim($this->accountCountryCode), '+');

        $this->validate([
            'accountName' => 'required|string|max:100',
            'accountCountryCode' => ['nullable', 'regex:/^\d{1,4}$/'],
            'accountPhone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\-()]+$/'],
        ], [
            'accountCountryCode.regex' => 'Use 1–4 digits, e.g. 971.',
            'accountPhone.regex' => 'Use digits, spaces, dashes or brackets only.',
        ], [
            'accountName' => 'name',
            'accountPhone' => 'phone number',
            'accountCountryCode' => 'country code',
        ]);

        auth()->user()->update([
            'name' => $this->accountName,
            'phone' => $this->accountPhone ?: null,
            'country_code' => $this->accountCountryCode ?: null,
        ]);

        $this->accountSaved = true;
    }

    public function openStatusComposer(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->inboxTab = 'status';
        $this->statusComposerOpen = true;
        $this->selectedStatusId = null;
        $this->statusText = '';
        $this->resetValidation('statusText');
    }

    public function closeStatusComposer(): void
    {
        $this->statusComposerOpen = false;
        $this->statusText = '';
        $this->resetValidation('statusText');
    }

    public function setStatusBg(string $color): void
    {
        if (in_array($color, self::STATUS_COLORS, true)) {
            $this->statusBg = $color;
        }
    }

    /**
     * Text status — same shape as Chat::postTextStatus() so customers'
     * Status tab renders it identically. Photo/video statuses go through
     * the shared /status/upload endpoint and land in statusUploaded().
     */
    public function postTextStatus(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->statusText = trim($this->statusText);
        $this->validate(['statusText' => 'required|string|max:500'], [], ['statusText' => 'status']);

        $status = Status::create([
            'user_id' => auth()->id(),
            'type' => 'text',
            'content' => $this->statusText,
            'background_color' => in_array($this->statusBg, self::STATUS_COLORS, true) ? $this->statusBg : '#075E54',
            'text_color' => '#ffffff',
        ]);

        $this->closeStatusComposer();
        $this->selectedStatusId = $status->id;
    }

    public function statusUploaded(int $statusId): void
    {
        if (Status::where('user_id', auth()->id())->whereKey($statusId)->exists()) {
            $this->inboxTab = 'status';
            $this->statusComposerOpen = false;
            $this->selectedStatusId = $statusId;
        }
    }

    public function viewStatus(int $statusId): void
    {
        if (Status::where('user_id', auth()->id())->whereKey($statusId)->exists()) {
            $this->statusComposerOpen = false;
            $this->selectedStatusId = $statusId;
        }
    }

    public function closeStatusViewer(): void
    {
        $this->selectedStatusId = null;
    }

    public function deleteStatus(int $statusId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $status = Status::where('user_id', auth()->id())->find($statusId);
        if (!$status) return;

        if ($status->media_path) {
            $path = storage_path('app/public/' . $status->media_path);
            if (is_file($path)) @unlink($path);
        }
        $status->delete();

        if ($this->selectedStatusId === $statusId) {
            $this->selectedStatusId = null;
        }
    }

    public function openQuickReplyForm(?int $quickReplyId = null): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $quickReply = $quickReplyId ? SupportQuickReply::find($quickReplyId) : null;
        $this->editingQuickReplyId = $quickReply?->id;
        $this->quickReplyShortcut = $quickReply?->shortcut ?? '';
        $this->quickReplyMessage = $quickReply?->message ?? '';
        $this->resetValidation(['quickReplyShortcut', 'quickReplyMessage']);
        $this->showQuickReplyForm = true;
    }

    public function closeQuickReplyForm(): void
    {
        $this->showQuickReplyForm = false;
        $this->editingQuickReplyId = null;
        $this->quickReplyShortcut = '';
        $this->quickReplyMessage = '';
        $this->resetValidation(['quickReplyShortcut', 'quickReplyMessage']);
    }

    public function saveQuickReply(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->quickReplyShortcut = SupportQuickReply::normalizeShortcut($this->quickReplyShortcut);
        $this->quickReplyMessage = trim($this->quickReplyMessage);

        $this->validate([
            'quickReplyShortcut' => [
                'required', 'max:30', 'regex:/^[a-z0-9_-]+$/',
                Rule::unique('support_quick_replies', 'shortcut')->ignore($this->editingQuickReplyId),
            ],
            'quickReplyMessage' => 'required|string|max:2000',
        ], [
            'quickReplyShortcut.regex' => 'Use letters, numbers, dashes or underscores only.',
            'quickReplyShortcut.unique' => 'Another quick reply already uses this shortcut.',
        ], [
            'quickReplyShortcut' => 'shortcut',
            'quickReplyMessage' => 'message',
        ]);

        $attributes = ['shortcut' => $this->quickReplyShortcut, 'message' => $this->quickReplyMessage];
        if ($this->editingQuickReplyId) {
            SupportQuickReply::whereKey($this->editingQuickReplyId)->update($attributes);
        } else {
            SupportQuickReply::create($attributes + ['created_by' => auth()->id()]);
        }

        $this->closeQuickReplyForm();
    }

    public function deleteQuickReply(int $quickReplyId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        SupportQuickReply::whereKey($quickReplyId)->delete();
    }

    public function setMediaType(string $type): void
    {
        if (in_array($type, ['media', 'audio', 'docs', 'links'], true)) {
            $this->mediaType = $type;
            $this->mediaLimit = 60;
        }
    }

    public function setMediaConversation(?int $conversationId = null): void
    {
        if ($conversationId !== null && !Conversation::support()->whereKey($conversationId)->exists()) return;

        $this->mediaConversationId = $conversationId;
        $this->mediaLimit = 60;
    }

    public function loadMoreMedia(): void
    {
        $this->mediaLimit = min($this->mediaLimit + 60, 600);
    }

    /**
     * Open the chat a Media-tab item belongs to and scroll to that message.
     * The conversation is derived server-side so a tampered id can only
     * ever land on a support thread.
     */
    public function openMediaMessage(int $messageId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $message = Message::whereKey($messageId)
            ->whereHas('conversation', fn ($query) => $query->where('is_support', true))
            ->first(['id', 'conversation_id']);
        if (!$message) return;

        $this->inboxTab = 'chat';
        $this->selectConversation((int) $message->conversation_id);
        $this->dispatch('si-jump-to-message', messageId: $message->id);
    }

    /**
     * Base query for one Media-tab bucket, across every support thread.
     * Buckets follow WhatsApp's split: photos/videos, audio, documents and
     * text messages that carry a link.
     */
    protected function supportMediaQuery(string $type)
    {
        $query = Message::query()->whereIn('conversation_id', Conversation::support()->select('id'));

        return match ($type) {
            'audio' => $query->where('attachment_type', 'audio')->whereNotNull('attachment_path'),
            'docs' => $query->where('attachment_type', 'file')->whereNotNull('attachment_path'),
            'links' => $query
                ->where(function ($query) {
                    $query->where('message', 'like', '%http://%')->orWhere('message', 'like', '%https://%');
                })
                ->where(function ($query) {
                    $query->whereNull('attachment_type')->orWhereNotIn('attachment_type', ['call', 'wallet_receipt']);
                }),
            default => $query->whereIn('attachment_type', ['image', 'video'])->whereNotNull('attachment_path'),
        };
    }

    public function getSupportMedia()
    {
        $messages = $this->supportMediaQuery($this->mediaType)
            ->when($this->mediaConversationId, fn ($query) => $query->where('conversation_id', $this->mediaConversationId))
            ->with(['conversation.userOne', 'sender'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($this->mediaLimit + 1)
            ->get();

        return $messages->map(function (Message $message) {
            $customer = $message->conversation?->userOne;
            preg_match('~https?://[^\s<>"\']+~i', (string) $message->message, $link);

            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'type' => $message->attachment_type,
                'url' => $message->attachment_url,
                'thumbnail_url' => $message->attachment_thumbnail_url,
                'name' => $message->attachment_original_name,
                'size' => $message->attachment_size,
                'duration' => $message->attachment_duration,
                'message' => (string) $message->message,
                'link' => $link[0] ?? null,
                'customer_name' => $customer?->name ?? $customer?->email ?? 'Unknown',
                'from_support' => (bool) ($message->sender?->is_admin ?? false),
                'created_at' => $message->created_at,
                'date_key' => $message->created_at->format('Y-m-d'),
                'date_label' => $this->dateLabel($message->created_at),
            ];
        });
    }

    protected function dateLabel($date): string
    {
        $day = \Carbon\Carbon::parse($date)->startOfDay();
        $today = \Carbon\Carbon::today();

        if ($day->equalTo($today)) return 'Today';
        if ($day->equalTo($today->copy()->subDay())) return 'Yesterday';
        if ($day->gt($today->copy()->subDays(7))) return $day->format('l');
        if ($day->year === $today->year) return $day->format('j F');
        return $day->format('j F Y');
    }

    /**
     * Customers barred from calling, for Settings → Blocked.
     */
    public function getBlockedCustomers()
    {
        $customers = User::query()
            ->where('calls_blocked', true)
            ->where(function ($query) {
                $query->where('is_admin', false)->orWhereNull('is_admin');
            })
            ->orderBy('name')
            ->limit(200)
            ->get(['id', 'name', 'email', 'avatar']);

        $conversationIds = Conversation::support()
            ->whereIn('user_one_id', $customers->pluck('id'))
            ->pluck('id', 'user_one_id');

        return $customers->map(fn (User $customer) => [
            'id' => $customer->id,
            'name' => $customer->name ?: $customer->email,
            'email' => $customer->email,
            'avatar' => $customer->avatar ? user_avatar_url($customer) : null,
            'conversation_id' => $conversationIds[$customer->id] ?? null,
        ]);
    }

    public function unblockCustomerCalls(int $userId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $customer = User::query()
            ->whereKey($userId)
            ->where(function ($query) {
                $query->where('is_admin', false)->orWhereNull('is_admin');
            })
            ->first();
        if (!$customer) return;

        $customer->calls_blocked = false;
        $customer->save();

        if ($this->selectedCustomer && (int) $this->selectedCustomer->id === $userId) {
            $this->selectedCustomer->calls_blocked = false;
        }
    }

    /**
     * All support conversations, most recently active first.
     */
    public function getConversations()
    {
        $countryLookups = $this->countryNameLookups();
        $conversations = Conversation::support()
            ->with(['userOne', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        return $conversations
            ->map(function ($conv) use ($countryLookups) {
                $customer = $conv->userOne;
                $latestCustomerMessageAt = $customer
                    ? Message::where('conversation_id', $conv->id)
                        ->where('sender_id', $customer->id)
                        ->where(function ($query) {
                            $query->whereNull('attachment_type')->orWhere('attachment_type', '!=', 'call');
                        })
                        ->max('created_at')
                    : null;
                // Auto replies don't count — the customer is still waiting for a person.
                $latestAdminReplyAt = Message::where('conversation_id', $conv->id)
                    ->whereHas('sender', fn ($query) => $query->where('is_admin', true))
                    ->where(function ($query) {
                        $query->whereNull('attachment_type')->orWhereNotIn('attachment_type', ['call', SupportAutoReply::TYPE]);
                    })
                    ->max('created_at');

                return [
                    'id' => $conv->id,
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer?->name ?? $customer?->email ?? 'Unknown',
                    'customer_country' => $this->resolveCustomerCountry($customer, $countryLookups),
                    'customer_email' => $customer?->email ?? '',
                    // Served via /u/{id}/avatar — public/storage isn't linked on this host,
                    // and Google avatars are absolute URLs.
                    'customer_avatar' => $customer?->avatar ? user_avatar_url($customer) : null,
                    'calls_blocked' => (bool) ($customer?->calls_blocked ?? false),
                    'last_message' => $conv->latestMessage?->message ?? '',
                    'last_message_at' => $conv->last_message_at,
                    'unanswered' => $latestCustomerMessageAt
                        && (!$latestAdminReplyAt || $latestCustomerMessageAt > $latestAdminReplyAt),
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

    public function getSupportCalls()
    {
        $supportConversationIds = Conversation::support()->select('id');

        return Call::with(['caller', 'callee'])
            ->whereIn('conversation_id', $supportConversationIds)
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function (Call $call) {
                $callerIsAdmin = (bool) ($call->caller?->is_admin ?? false);
                $customer = $callerIsAdmin ? $call->callee : $call->caller;
                $isUnanswered = !$callerIsAdmin && in_array($call->status, ['missed', 'declined'], true);

                return [
                    'id' => $call->id,
                    'conversation_id' => $call->conversation_id,
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer?->name ?? $customer?->email ?? 'Unknown',
                    'customer_avatar' => $customer ? user_avatar_url($customer) : null,
                    'type' => $call->type,
                    'status' => $call->status,
                    'direction' => $callerIsAdmin ? 'Outgoing' : 'Incoming',
                    'is_unanswered' => $isUnanswered,
                    'started_at' => $call->started_at ?? $call->created_at,
                    'duration' => $call->duration,
                ];
            })
            ->when($this->searchTerm !== '', function ($calls) {
                $search = strtolower($this->searchTerm);
                return $calls->filter(fn ($call) => str_contains(strtolower($call['customer_name']), $search));
            });
    }

    protected function countryNameLookups(): array
    {
        $countries = Country::query()->get(['nicename', 'iso', 'phonecode']);
        $byDialCode = [];
        $byIsoCode = [];

        foreach ($countries as $country) {
            $dialCode = preg_replace('/\D+/', '', (string) $country->phonecode);
            if ($dialCode !== '') {
                if (!array_key_exists($dialCode, $byDialCode)) {
                    $byDialCode[$dialCode] = $country->nicename;
                } elseif ($byDialCode[$dialCode] !== $country->nicename) {
                    $byDialCode[$dialCode] = null;
                }
            }

            $isoCode = strtoupper(trim((string) $country->iso));
            if ($isoCode !== '') $byIsoCode[$isoCode] = $country->nicename;
        }

        return ['dial' => $byDialCode, 'iso' => $byIsoCode];
    }

    protected function resolveCustomerCountry(?User $customer, array $lookups): string
    {
        if (!$customer) return '';

        $registrationCountry = trim((string) $customer->registration_country);
        if ($registrationCountry !== '' && !in_array(strtolower($registrationCountry), ['local', 'unknown', 'n/a'], true)) {
            return $registrationCountry;
        }

        $storedCode = trim((string) $customer->country_code);
        $dialCode = preg_replace('/\D+/', '', $storedCode);
        if ($dialCode !== '' && !empty($lookups['dial'][$dialCode])) {
            return $lookups['dial'][$dialCode];
        }

        return $lookups['iso'][strtoupper($storedCode)] ?? '';
    }

    public function selectConversation(int $conversationId): void
    {
        $conversation = Conversation::support()->find($conversationId);
        if (!$conversation) return;

        // Media / Settings / Starred all hand off to the thread view.
        if (!in_array($this->inboxTab, ['chat', 'calls'], true)) {
            $this->inboxTab = 'chat';
        }

        $this->selectedConversationId = $conversationId;
        $this->selectedCustomer = $conversation->userOne;
        $this->selectedCustomerCountry = $this->resolveCustomerCountry($this->selectedCustomer, $this->countryNameLookups());
        $this->cancelReply();
        $this->closeForward();
        $this->starredViewOpen = false;
        $this->dismissSuggestion();
        $this->loadMessages();
        $this->markAsRead();
        $this->dispatch('message-received');
    }

    public function startReply(int $messageId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $parent = Message::with('sender')
            ->whereKey($messageId)
            ->where('conversation_id', $this->selectedConversationId)
            ->first();
        if (!$parent || !Conversation::support()->whereKey($this->selectedConversationId)->exists()) return;

        $isMine = (int) $parent->sender_id === (int) auth()->id();
        $senderName = $isMine
            ? 'You'
            : (($parent->sender?->is_admin ?? false)
                ? 'Support · ' . ($parent->sender->name ?? $parent->sender->email)
                : ($parent->sender?->name ?? $parent->sender?->email ?? 'Customer'));
        $excerpt = trim((string) $parent->message);
        if ($excerpt === '') {
            $excerpt = match ($parent->attachment_type) {
                'image' => 'Photo',
                'audio' => 'Voice message',
                'video' => 'Video',
                'call' => 'Call',
                'wallet_receipt' => 'Wallet top-up receipt',
                default => $parent->attachment_original_name ?: 'Attachment',
            };
        }

        $this->replyingToMessageId = $parent->id;
        $this->replyingToPreview = [
            'sender_name' => $senderName,
            'excerpt' => Str::limit($excerpt, 100),
        ];
    }

    public function cancelReply(): void
    {
        $this->replyingToMessageId = null;
        $this->replyingToPreview = null;
    }

    public function startForward(int $messageId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $source = Message::whereKey($messageId)
            ->where('conversation_id', $this->selectedConversationId)
            ->first();
        if (!$source || !Conversation::support()->whereKey($this->selectedConversationId)->exists()) return;

        $this->forwardingMessageId = $source->id;
        $this->forwardConversationIds = [];
        $this->forwardSearch = '';
        $this->showForwardModal = true;
    }

    public function closeForward(): void
    {
        $this->showForwardModal = false;
        $this->forwardingMessageId = null;
        $this->forwardConversationIds = [];
        $this->forwardSearch = '';
    }

    public function getForwardTargets()
    {
        if (!$this->showForwardModal) return collect();

        return Conversation::support()
            ->whereKeyNot($this->selectedConversationId ?? 0)
            ->whereHas('userOne', function ($query) {
                $query->where(function ($query) {
                    $query->where('is_admin', false)->orWhereNull('is_admin');
                });
            })
            ->with('userOne')
            ->when($this->forwardSearch !== '', function ($query) {
                $search = '%' . addcslashes($this->forwardSearch, '%_\\') . '%';
                $query->whereHas('userOne', function ($query) use ($search) {
                    $query->where('name', 'like', $search)->orWhere('email', 'like', $search);
                });
            })
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();
    }

    public function forwardSelectedMessage(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->validate([
            'forwardConversationIds' => 'required|array|min:1|max:5',
            'forwardConversationIds.*' => 'integer|distinct|exists:conversations,id',
        ]);

        $source = Message::whereKey($this->forwardingMessageId)
            ->where('conversation_id', $this->selectedConversationId)
            ->first();
        if (!$source || !Conversation::support()->whereKey($this->selectedConversationId)->exists()) {
            $this->closeForward();
            return;
        }

        $targetIds = array_values(array_unique(array_map('intval', $this->forwardConversationIds)));
        $targets = Conversation::support()
            ->whereIn('id', $targetIds)
            ->whereKeyNot($this->selectedConversationId)
            ->whereHas('userOne', function ($query) {
                $query->where(function ($query) {
                    $query->where('is_admin', false)->orWhereNull('is_admin');
                });
            })
            ->with('userOne')
            ->get();

        if ($targets->count() !== count($targetIds)) {
            $this->addError('forwardConversationIds', 'Choose up to five valid support chats.');
            return;
        }

        foreach ($targets as $target) {
            $forwarded = Message::create([
                'conversation_id' => $target->id,
                'sender_id' => auth()->id(),
                'message' => $source->message ?: '',
                'status' => 'sent',
                'attachment_path' => $source->attachment_path,
                'attachment_disk' => $source->attachment_disk,
                'attachment_thumbnail_path' => $source->attachment_thumbnail_path,
                // A forwarded auto reply is an agent's message in its new thread.
                'attachment_type' => $source->attachment_type === SupportAutoReply::TYPE ? null : $source->attachment_type,
                'attachment_mime' => $source->attachment_mime,
                'attachment_size' => $source->attachment_size,
                'attachment_duration' => $source->attachment_duration,
                'attachment_original_name' => $source->attachment_original_name,
            ]);

            $target->update(['last_message_at' => now()]);

            try {
                broadcast(new NewChatMessage($forwarded, (int) $target->user_one_id))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Support message forward broadcast failed', [
                    'message_id' => $forwarded->id,
                    'conversation_id' => $target->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $sentCount = $targets->count();
        $this->closeForward();
        session()->flash('forward-success', "Message forwarded to {$sentCount} support chats.");
    }

    public function deleteMessage(int $messageId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $conversation = $this->selectedSupportConversation();
        if (!$conversation) return;

        $message = Message::whereKey($messageId)
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', auth()->id())
            ->first();
        if (!$message) return;

        if ($message->attachment_path) {
            $isSharedAttachment = Message::where('attachment_path', $message->attachment_path)
                ->where('id', '!=', $message->id)
                ->exists();
            if (!$isSharedAttachment) {
                try {
                    Storage::disk($message->attachment_disk ?: 'public')->delete($message->attachment_path);
                } catch (\Throwable $e) {
                    Log::warning('Support message attachment cleanup failed', ['message_id' => $message->id, 'error' => $e->getMessage()]);
                }
            }
        }

        $message->delete();
        $conversation->update([
            'last_message_at' => Message::where('conversation_id', $conversation->id)->max('created_at'),
        ]);
        $this->loadMessages();
        $this->dispatch('message-received');
    }

    /**
     * Bar (or un-bar) the selected customer from placing calls at all.
     *
     * Enforced server-side in CallController::signal() on every offer, support
     * ones included. calls_blocked is deliberately absent from User::$fillable
     * so this moderation path is the only way it can be written.
     */
    public function toggleCallBlock(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        if (!$this->selectedConversationId || !$this->selectedCustomer) return;

        // Re-check the conversation is still a support thread before writing
        // to another user's record.
        if (!Conversation::support()->whereKey($this->selectedConversationId)->exists()) return;

        $blocked = !((bool) ($this->selectedCustomer->calls_blocked ?? false));
        $this->selectedCustomer->calls_blocked = $blocked;
        $this->selectedCustomer->save();
    }

    public function openWalletTopup(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        if (!$this->selectedSupportConversation()) return;

        $this->walletTopupAmount = '';
        $this->walletTopupNote = '';
        $this->resetValidation(['walletTopupAmount', 'walletTopupNote']);
        $this->showWalletTopupModal = true;
    }

    public function closeWalletTopup(): void
    {
        $this->showWalletTopupModal = false;
        $this->walletTopupAmount = '';
        $this->walletTopupNote = '';
        $this->resetValidation(['walletTopupAmount', 'walletTopupNote']);
    }

    public function topUpSelectedUser(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $this->validate([
            'walletTopupAmount' => 'required|numeric|min:0.01|max:1000000',
            'walletTopupNote' => 'nullable|string|max:255',
        ]);

        $conversation = $this->selectedSupportConversation();
        if (!$conversation) {
            $this->addError('walletTopupAmount', 'Select a support conversation first.');
            return;
        }

        $customer = $conversation->userOne;
        if (!$customer || ($customer->is_admin ?? false)) {
            $this->addError('walletTopupAmount', 'This support conversation has no eligible customer.');
            return;
        }

        $amount = round((float) $this->walletTopupAmount, 2);
        $note = trim($this->walletTopupNote) ?: 'Admin wallet top-up';

        try {
            [$wallet, $message] = DB::transaction(function () use ($conversation, $customer, $amount, $note) {
                Wallet::firstOrCreate(
                    ['user_id' => $customer->id],
                    ['balance' => 0]
                );

                $wallet = Wallet::where('user_id', $customer->id)->lockForUpdate()->firstOrFail();
                $wallet->balance = round((float) $wallet->balance + $amount, 2);
                $wallet->save();

                $transaction = WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'description' => $note,
                    'status' => 'completed',
                ]);

                $receiptLines = [
                    'Wallet Top-up Receipt',
                    'Amount: $' . number_format($amount, 2),
                    'New balance: $' . number_format((float) $wallet->balance, 2),
                    'Transaction: #' . $transaction->id,
                    'Note: ' . $note,
                ];

                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => auth()->id(),
                    'message' => implode("\n", $receiptLines),
                    'attachment_type' => 'wallet_receipt',
                    'status' => 'sent',
                ]);

                $conversation->update(['last_message_at' => now()]);

                return [$wallet, $message];
            });
        } catch (\Throwable $e) {
            Log::error('Support wallet top-up failed', [
                'admin_id' => auth()->id(),
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            $this->addError('walletTopupAmount', 'Top-up failed. No balance change was saved.');
            return;
        }

        $this->selectedCustomer->setRelation('wallet', $wallet);
        try {
            broadcast(new NewChatMessage($message, (int) $customer->id))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Support wallet receipt broadcast failed', [
                'message_id' => $message->id,
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->closeWalletTopup();
        $this->loadMessages();
        $this->dispatch('message-received');
    }

    protected function selectedSupportConversation(): ?Conversation
    {
        if (!$this->selectedConversationId) return null;

        return Conversation::support()
            ->with('userOne')
            ->find($this->selectedConversationId);
    }

    public function toggleSupportCallBlock(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $setting = Setting::query()->find(1);
        if (!$setting) return;

        $setting->support_calls_blocked = !$setting->support_calls_blocked;
        $setting->save();
        $this->supportCallsBlocked = (bool) $setting->support_calls_blocked;
    }

    public function loadMessages(): void
    {
        if (!$this->selectedConversationId) {
            $this->conversationMessages = [];
            return;
        }

        $adminId = auth()->id();
        $messages = Message::where('conversation_id', $this->selectedConversationId)
            ->with(['sender', 'replyTo.sender'])
            ->orderBy('created_at', 'asc')
            ->get();
        $starredIds = $messages->isEmpty()
            ? collect()
            : DB::table('message_stars')
                ->whereIn('message_id', $messages->pluck('id'))
                ->where('user_id', $adminId)
                ->pluck('message_id')
                ->flip();

        $this->conversationMessages = $messages->map(function ($msg) use ($adminId, $starredIds) {
            $isAdmin = $msg->sender && ($msg->sender->is_admin ?? false);
            $replyTo = $msg->replyTo;
            $replyExcerpt = trim((string) ($replyTo?->message ?? ''));
            if ($replyTo && $replyExcerpt === '') {
                $replyExcerpt = match ($replyTo->attachment_type) {
                    'image' => 'Photo',
                    'audio' => 'Voice message',
                    'video' => 'Video',
                    'call' => 'Call',
                    'wallet_receipt' => 'Wallet top-up receipt',
                    default => $replyTo->attachment_original_name ?: 'Attachment',
                };
            }

            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender_name' => $isAdmin
                    ? ('Support · ' . ($msg->sender->name ?? $msg->sender->email))
                    : ($msg->sender?->name ?? $msg->sender?->email ?? 'Customer'),
                'is_mine' => $isAdmin,
                'is_me' => $msg->sender_id === $adminId,
                'is_auto_reply' => $msg->attachment_type === SupportAutoReply::TYPE,
                'is_starred' => $starredIds->has($msg->id),
                'status' => $msg->status,
                'created_at' => $msg->created_at->toISOString(),
                'attachment_url' => $msg->attachment_url,
                'attachment_thumbnail_url' => $msg->attachment_thumbnail_url,
                'attachment_type' => $msg->attachment_type,
                'attachment_mime' => $msg->attachment_mime,
                'attachment_size' => $msg->attachment_size,
                'attachment_duration' => $msg->attachment_duration,
                'attachment_original_name' => $msg->attachment_original_name,
                'reply_to' => $replyTo ? [
                    'id' => $replyTo->id,
                    'sender_name' => ($replyTo->sender_id === $adminId)
                        ? 'You'
                        : (($replyTo->sender?->is_admin ?? false)
                            ? 'Support · ' . ($replyTo->sender->name ?? $replyTo->sender->email)
                            : ($replyTo->sender?->name ?? $replyTo->sender?->email ?? 'Customer')),
                    'excerpt' => Str::limit($replyExcerpt, 100),
                ] : null,
            ];
        })->toArray();
    }

    public function toggleStar(int $messageId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $message = Message::whereKey($messageId)
            ->where('conversation_id', $this->selectedConversationId)
            ->whereHas('conversation', fn ($query) => $query->where('is_support', true))
            ->first();
        if (!$message) return;

        $star = DB::table('message_stars')
            ->where('user_id', auth()->id())
            ->where('message_id', $message->id);

        if ($star->exists()) {
            $star->delete();
        } else {
            DB::table('message_stars')->insertOrIgnore([
                'user_id' => auth()->id(),
                'message_id' => $message->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->loadMessages();
    }

    public function suggestReply(int $botId): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);
        if (!$this->selectedConversationId) return;

        $bot = SupportBot::active()
            ->forTask(SupportBot::TASK_SUPPORT_CHAT)
            ->whereKey($botId)
            ->first();

        if (!$bot) {
            $this->suggestedReply = 'That bot is not available.';
            return;
        }

        $conversation = Conversation::support()->find($this->selectedConversationId);
        if (!$conversation) return;

        $this->suggestingReply = true;
        try {
            $this->suggestedReply = app(GrokSupportBot::class)->draftReply($bot, $conversation);
            $this->suggestedByBotId = $bot->id;
        } catch (\Throwable $e) {
            $this->suggestedReply = 'Could not draft a reply: ' . $e->getMessage();
            $this->suggestedByBotId = null;
        } finally {
            $this->suggestingReply = false;
        }
    }

    public function useSuggestion(): void
    {
        if ($this->suggestedReply === '') return;
        $this->reply = $this->suggestedReply;
        $this->dismissSuggestion();
    }

    public function dismissSuggestion(): void
    {
        $this->suggestedReply = '';
        $this->suggestedByBotId = null;
    }

    public function openStarredView(): void
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);
        $this->starredViewOpen = true;
    }

    public function closeStarredView(): void
    {
        $this->starredViewOpen = false;
    }

    /**
     * Starred messages for this admin, scoped to support conversations only.
     * Shape mirrors Chat::getStarredMessages() so the admin overlay reuses
     * the same row layout as the user-side list.
     */
    public function getStarredMessages()
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $adminId = auth()->id();

        $msgIds = DB::table('message_stars')
            ->where('user_id', $adminId)
            ->orderByDesc('created_at')
            ->limit(200)
            ->pluck('message_id');

        if ($msgIds->isEmpty()) return collect();

        return Message::whereIn('id', $msgIds)
            ->whereHas('conversation', fn ($q) => $q->where('is_support', true))
            ->with(['sender', 'conversation'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($m) use ($adminId) {
                $peer = $m->conversation?->getOtherUser($adminId);
                return [
                    'id'               => $m->id,
                    'conversation_id'  => $m->conversation_id,
                    'message'          => $m->message,
                    'sender_name'      => $m->sender?->name ?? $m->sender?->email ?? 'Unknown',
                    'is_mine'          => $m->sender_id === $adminId,
                    'created_at'       => $m->created_at->toISOString(),
                    'attachment_type'  => $m->attachment_type,
                    'attachment_url'   => $m->attachment_url,
                    'peer_name'        => $peer?->name ?? $peer?->email ?? '',
                ];
            });
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

        $replyToId = $this->replyingToMessageId
            && Message::whereKey($this->replyingToMessageId)
                ->where('conversation_id', $conversation->id)
                ->exists()
                ? $this->replyingToMessageId
                : null;

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            // messages.message is NOT NULL — use '' for attachment-only rows.
            'message' => $this->reply ?: '',
            'status' => 'sent',
            'reply_to_id' => $replyToId,
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
        $this->cancelReply();
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
        $supportCalls = $this->getSupportCalls();
        $supportConversationIds = Conversation::support()->select('id');
        $unansweredCallsCount = Call::whereIn('conversation_id', $supportConversationIds)
            ->whereIn('status', ['missed', 'declined'])
            ->whereHas('caller', function ($query) {
                $query->where(function ($query) {
                    $query->where('is_admin', false)->orWhereNull('is_admin');
                });
            })
            ->count();
        $broadcastLocationUserCount = 0;

        if ($this->showBroadcastModal && $this->broadcastAudience === 'location'
            && $this->broadcastCountryId !== '' && !empty($this->broadcastCityIds)) {
            $country = Country::query()->find($this->broadcastCountryId);
            if ($country) {
                $validCityIds = City::query()
                    ->where('country', $country->nicename)
                    ->whereIn('id', $this->broadcastCityIds)
                    ->select('id');

                $matchingUserIds = UsersProfile::query()
                    ->whereIn('city', $validCityIds)
                    ->whereNotNull('user_id')
                    ->select('user_id')
                    ->distinct();

                $broadcastLocationUserCount = $this->eligibleBroadcastUsers()
                    ->whereIn('id', $matchingUserIds)
                    ->count();
            }
        }

        $mediaItems = collect();
        $mediaTypeCounts = [];
        $mediaConversationCounts = collect();
        $mediaCustomerName = null;
        if ($this->inboxTab === 'media') {
            if ($this->mediaConversationId) {
                $mediaCustomer = Conversation::support()->with('userOne')->find($this->mediaConversationId)?->userOne;
                $mediaCustomerName = $mediaCustomer?->name ?: ($mediaCustomer?->email ?? 'Customer');
            }
            $mediaItems = $this->getSupportMedia();
            foreach (['media', 'audio', 'docs', 'links'] as $type) {
                $mediaTypeCounts[$type] = $this->supportMediaQuery($type)
                    ->when($this->mediaConversationId, fn ($query) => $query->where('conversation_id', $this->mediaConversationId))
                    ->count();
            }
            $mediaConversationCounts = $this->supportMediaQuery($this->mediaType)
                ->selectRaw('conversation_id, COUNT(*) as aggregate')
                ->groupBy('conversation_id')
                ->pluck('aggregate', 'conversation_id');
        }

        $myStatuses = collect();
        $selectedStatus = null;
        if ($this->inboxTab === 'status') {
            $myStatuses = Status::where('user_id', auth()->id())
                ->active()
                ->withCount('views')
                ->orderByDesc('created_at')
                ->get();
            $selectedStatus = $this->selectedStatusId ? $myStatuses->firstWhere('id', $this->selectedStatusId) : null;
            if ($selectedStatus) {
                $selectedStatus->setRelation('views', StatusView::with('viewer')
                    ->where('status_id', $selectedStatus->id)
                    ->orderByDesc('viewed_at')
                    ->get());
            }
        }

        // /u/{id}/avatar is cached for a day, so a new upload would keep
        // showing the old photo. Key the URL on the stored path instead.
        $admin = auth()->user();
        $meAvatarUrl = user_avatar_url($admin);
        if ($meAvatarUrl && !preg_match('~^(https?:)?//~', (string) $admin->avatar)) {
            $meAvatarUrl .= '?v=' . substr(md5((string) $admin->avatar), 0, 10);
        }

        return view('livewire.admin.support-inbox', [
            'conversations' => $conversations,
            'myStatuses' => $myStatuses,
            'selectedStatus' => $selectedStatus,
            'meAvatarUrl' => $meAvatarUrl,
            'mediaItems' => $mediaItems->take($this->mediaLimit),
            'mediaHasMore' => $mediaItems->count() > $this->mediaLimit,
            'mediaTypeCounts' => $mediaTypeCounts,
            'mediaConversationCounts' => $mediaConversationCounts,
            'mediaCustomerName' => $mediaCustomerName,
            'blockedCustomers' => $this->inboxTab === 'settings' ? $this->getBlockedCustomers() : collect(),
            'quickReplies' => SupportQuickReply::query()->orderBy('shortcut')->get(['id', 'shortcut', 'message']),
            'supportCalls' => $supportCalls,
            'unansweredChatCount' => $conversations->where('unanswered', true)->count(),
            'unansweredCallsCount' => $unansweredCallsCount,
            'broadcastUsers' => $this->showBroadcastModal ? $this->getBroadcastUsers() : collect(),
            'broadcastCountries' => $this->showBroadcastModal ? $this->getBroadcastCountries() : collect(),
            'broadcastCities' => $this->showBroadcastModal && $this->broadcastAudience === 'location'
                ? $this->getBroadcastCities()
                : collect(),
            'broadcastUserCount' => $this->eligibleBroadcastUsers()->count(),
            'broadcastLocationUserCount' => $broadcastLocationUserCount,
            'forwardTargets' => $this->showForwardModal ? $this->getForwardTargets() : collect(),
            'availableBots' => SupportBot::active()
                ->forTask(SupportBot::TASK_SUPPORT_CHAT)
                ->orderBy('name')
                ->get(['id', 'name', 'description']),
            'grokConfigured' => !empty(config('services.grok.api_key')),
        ])->layout('admin.layout.master-slot');
    }
}
