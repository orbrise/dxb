<?php

namespace App\Jobs;

use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSupportBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $senderId,
        public string $messageText,
        public ?array $recipientIds = null,
        public ?array $attachment = null,
    ) {}

    public function handle(): void
    {
        $query = User::query()
            ->where(function ($query) {
                $query->where('is_admin', false)->orWhereNull('is_admin');
            });

        if ($this->recipientIds !== null) {
            $query->whereIn('id', $this->recipientIds);
        }

        $query->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $conversation = Conversation::getOrCreateSupport((int) $user->id);
                $messageData = [
                    'conversation_id' => $conversation->id,
                    'sender_id' => $this->senderId,
                    'message' => $this->messageText,
                    'status' => 'sent',
                ];
                if ($this->attachment) {
                    $messageData += [
                        'attachment_path' => $this->attachment['path'],
                        'attachment_disk' => $this->attachment['disk'],
                        'attachment_type' => $this->attachment['type'],
                        'attachment_mime' => $this->attachment['mime'],
                        'attachment_size' => $this->attachment['size'],
                        'attachment_original_name' => $this->attachment['name'],
                    ];
                }

                $message = Message::create($messageData);

                $conversation->update(['last_message_at' => now()]);

                try {
                    broadcast(new NewChatMessage($message, (int) $user->id))->toOthers();
                } catch (\Throwable $e) {
                    Log::warning('Support broadcast delivery event failed', [
                        'user_id' => $user->id,
                        'message_id' => $message->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }
}