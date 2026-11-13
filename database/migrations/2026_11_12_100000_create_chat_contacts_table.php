<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Who each user has talked to (messaged, or called), for the free plan's
 * people limit (App\Support\ChatAllowance). Filled from the existing chats
 * and calls so everyone keeps the people they already talk to.
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('chat_contacts')) {
            Schema::create('chat_contacts', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('peer_id');
                $table->timestamp('created_at')->nullable();
                $table->primary(['user_id', 'peer_id']);
            });
        }

        // Everyone a user has sent a message to (Support chats aside).
        DB::statement("
            INSERT IGNORE INTO chat_contacts (user_id, peer_id, created_at)
            SELECT m.sender_id,
                   IF(c.user_one_id = m.sender_id, c.user_two_id, c.user_one_id),
                   MIN(m.created_at)
            FROM messages m
            JOIN conversations c ON c.id = m.conversation_id
            WHERE c.is_support = 0 AND m.sender_id IS NOT NULL
              AND c.user_one_id IS NOT NULL AND c.user_two_id IS NOT NULL
              AND m.sender_id IN (c.user_one_id, c.user_two_id)
            GROUP BY m.sender_id, IF(c.user_one_id = m.sender_id, c.user_two_id, c.user_one_id)
        ");

        // Everyone a user has called, and everyone whose call they answered.
        DB::statement("
            INSERT IGNORE INTO chat_contacts (user_id, peer_id, created_at)
            SELECT caller_id, callee_id, MIN(created_at) FROM calls
            WHERE is_support = 0 AND caller_id IS NOT NULL AND callee_id IS NOT NULL AND caller_id <> callee_id
            GROUP BY caller_id, callee_id
        ");
        DB::statement("
            INSERT IGNORE INTO chat_contacts (user_id, peer_id, created_at)
            SELECT callee_id, caller_id, MIN(created_at) FROM calls
            WHERE is_support = 0 AND connected_at IS NOT NULL
              AND caller_id IS NOT NULL AND callee_id IS NOT NULL AND caller_id <> callee_id
            GROUP BY callee_id, caller_id
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_contacts');
    }
};
