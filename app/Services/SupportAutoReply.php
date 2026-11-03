<?php

namespace App\Services;

use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Support inbox "away" auto reply. Run every minute by the
 * support:auto-reply command; configured in Support Inbox → Settings →
 * Business tools → Auto reply.
 *
 * A customer gets one auto reply when their first unanswered message is
 * older than the configured delay and no agent has replied since. It is not
 * repeated while they keep waiting (for COOLDOWN_HOURS), and conversations
 * that were already waiting longer than WINDOW_MINUTES past the delay are
 * left alone, so switching the feature on never mass-replies to old chats.
 */
class SupportAutoReply
{
    public const TYPE = 'auto_reply';
    public const DELAYS = [1, 2, 5, 10, 15, 30, 60, 120];
    public const DEFAULT_MESSAGE = "Thank you for reaching us! A member of our support team will contact you shortly.";
    public const COOLDOWN_HOURS = 24;
    public const WINDOW_MINUTES = 60;

    /**
     * Send every auto reply that is due. Returns how many were sent.
     */
    public function sendDueReplies(): int
    {
        $setting = Setting::query()->find(1);
        if (!$setting || !$setting->support_auto_reply_enabled) return 0;

        $sender = $this->sender($setting);
        if (!$sender) {
            Log::warning('Support auto reply skipped: no admin account to send from.');
            return 0;
        }

        $delay = max(1, (int) ($setting->support_auto_reply_delay ?: 5));
        $dueBefore = now()->subMinutes($delay);
        $notBefore = $dueBefore->copy()->subMinutes(self::WINDOW_MINUTES);
        $template = trim((string) $setting->support_auto_reply_message) ?: self::DEFAULT_MESSAGE;

        // Support threads with a customer message inside the due window.
        $conversationIds = Message::query()
            ->join('conversations', 'conversations.id', '=', 'messages.conversation_id')
            ->where('conversations.is_support', true)
            ->whereColumn('messages.sender_id', 'conversations.user_one_id')
            ->whereBetween('messages.created_at', [$notBefore, $dueBefore])
            ->distinct()
            ->pluck('messages.conversation_id');

        $sent = 0;
        foreach (Conversation::support()->with('userOne')->whereIn('id', $conversationIds)->get() as $conversation) {
            if (!$this->isDue($conversation, $dueBefore, $notBefore)) continue;

            try {
                $this->send($conversation, $sender, $template);
                $sent++;
            } catch (\Throwable $e) {
                Log::error('Support auto reply failed', [
                    'conversation_id' => $conversation->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }

    protected function isDue(Conversation $conversation, $dueBefore, $notBefore): bool
    {
        $customerId = (int) $conversation->user_one_id;
        $notCall = function ($query) {
            $query->whereNull('attachment_type')->orWhere('attachment_type', '!=', 'call');
        };

        // Last thing support said — an agent reply or an earlier auto reply.
        $lastSupport = Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $customerId)
            ->where($notCall)
            ->orderByDesc('id')
            ->first(['id', 'attachment_type', 'created_at']);

        // When the customer started waiting.
        $firstWaiting = Message::where('conversation_id', $conversation->id)
            ->where('sender_id', $customerId)
            ->where($notCall)
            ->when($lastSupport, fn ($query) => $query->where('id', '>', $lastSupport->id))
            ->orderBy('id')
            ->first(['id', 'created_at']);

        if (!$firstWaiting) return false;                                  // support spoke last
        if ($firstWaiting->created_at->gt($dueBefore)) return false;       // not waited long enough yet
        if ($firstWaiting->created_at->lt($notBefore)) return false;       // stale — predates the window

        // Already auto-replied and still no agent: don't repeat for a while.
        return !($lastSupport
            && $lastSupport->attachment_type === self::TYPE
            && $lastSupport->created_at->gt(now()->subHours(self::COOLDOWN_HOURS)));
    }

    protected function send(Conversation $conversation, User $sender, string $template): void
    {
        $customer = $conversation->userOne;
        $displayName = $customer?->name ?: Str::before((string) $customer?->email, '@');
        $firstName = strtok(trim((string) $displayName), ' ') ?: '';
        $text = trim(preg_replace('/\{name\}/i', $firstName, $template));

        $reply = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'message' => $text,
            'attachment_type' => self::TYPE,
            'status' => 'sent',
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            broadcast(new NewChatMessage($reply, (int) $conversation->user_one_id));
        } catch (\Throwable $e) {
            Log::warning('Support auto reply broadcast failed', [
                'message_id' => $reply->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * The admin who last saved the setting, else the first admin account.
     */
    protected function sender(Setting $setting): ?User
    {
        $admins = User::query()->where('is_admin', true);

        return ($setting->support_auto_reply_sender_id
                ? (clone $admins)->whereKey($setting->support_auto_reply_sender_id)->first()
                : null)
            ?? $admins->orderBy('id')->first();
    }
}
