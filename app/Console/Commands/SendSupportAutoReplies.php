<?php

namespace App\Console\Commands;

use App\Services\SupportAutoReply;
use Illuminate\Console\Command;

class SendSupportAutoReplies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:auto-reply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the support auto reply to customers who have waited past the configured delay';

    /**
     * Execute the console command.
     */
    public function handle(SupportAutoReply $autoReply): int
    {
        $sent = $autoReply->sendDueReplies();
        $this->info("Sent {$sent} support auto " . ($sent === 1 ? 'reply' : 'replies') . '.');

        return self::SUCCESS;
    }
}
