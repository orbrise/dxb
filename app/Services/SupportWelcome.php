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
 * Welcome message a new user gets in their Support chat the first time they
 * log in. Configured in Support Inbox → Settings → Business tools → Welcome
 * message; sent from the admin who last saved it.
 *
 * users.support_welcomed_at marks who has had it. Accounts that existed when
 * the feature shipped were back-filled, so only new users are welcomed.
 */
class SupportWelcome
{
    public const DEFAULT_MESSAGE = <<<'TXT'
        👋 Welcome to Evoory ™, {name}!

        Thanks for joining us. This is your Support chat — message us here any time if you need help with your account, listings or payments.


        Regards,
        Evoory ™ Team
        TXT;

    /** Called on every login; only does anything the first time. */
    public static function sendOnFirstLogin(User $user): void
    {
        if ($user->is_admin || $user->support_welcomed_at) return;

        // Claim the welcome first so two logins at once can't both send it.
        $claimed = User::whereKey($user->id)
            ->whereNull('support_welcomed_at')
            ->update(['support_welcomed_at' => now()]);
        if (!$claimed) return;

        try {
            $setting = Setting::query()->find(1);
            if (!$setting || !$setting->support_welcome_enabled) return;

            $sender = self::sender($setting);
            if (!$sender) {
                Log::warning('Support welcome skipped: no admin account to send from.', ['user_id' => $user->id]);
                return;
            }

            $template = trim((string) $setting->support_welcome_message) ?: self::DEFAULT_MESSAGE;
            $displayName = $user->name ?: Str::before((string) $user->email, '@');
            $firstName = strtok(trim((string) $displayName), ' ') ?: '';
            $text = trim(str_ireplace('{name}', $firstName, $template));

            $conversation = Conversation::getOrCreateSupport((int) $user->id);
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'message' => $text,
                'status' => 'sent',
            ]);

            $conversation->update(['last_message_at' => now()]);
        } catch (\Throwable $e) {
            // Let the next login try again.
            User::whereKey($user->id)->update(['support_welcomed_at' => null]);
            Log::error('Support welcome failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return;
        }

        try {
            broadcast(new NewChatMessage($message, (int) $user->id));
        } catch (\Throwable $e) {
            Log::warning('Support welcome broadcast failed', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * The admin who last saved the setting, else the first admin account.
     */
    protected static function sender(Setting $setting): ?User
    {
        $admins = User::query()->where('is_admin', true);

        return ($setting->support_welcome_sender_id
                ? (clone $admins)->whereKey($setting->support_welcome_sender_id)->first()
                : null)
            ?? $admins->orderBy('id')->first();
    }
}
