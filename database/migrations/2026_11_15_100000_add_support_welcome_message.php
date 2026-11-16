<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Support chat message a new user gets on their first login
            // (SupportWelcome). NULL message = SupportWelcome::DEFAULT_MESSAGE.
            // utf8mb4 explicitly, like support_topup_message: emoji.
            if (!Schema::hasColumn('settings', 'support_welcome_enabled')) {
                $table->boolean('support_welcome_enabled')->default(true);
            }
            if (!Schema::hasColumn('settings', 'support_welcome_message')) {
                $table->text('support_welcome_message')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            }
            if (!Schema::hasColumn('settings', 'support_welcome_sender_id')) {
                $table->unsignedBigInteger('support_welcome_sender_id')->nullable();
            }
        });

        if (!Schema::hasColumn('users', 'support_welcomed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('support_welcomed_at')->nullable();
            });

            // Everyone who already has an account counts as welcomed, so only
            // users created from now on get the message.
            DB::table('users')->update(['support_welcomed_at' => now()]);
        }
    }

    public function down(): void
    {
        foreach (['support_welcome_enabled', 'support_welcome_message', 'support_welcome_sender_id'] as $column) {
            if (Schema::hasColumn('settings', $column)) {
                Schema::table('settings', fn (Blueprint $table) => $table->dropColumn($column));
            }
        }

        if (Schema::hasColumn('users', 'support_welcomed_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('support_welcomed_at'));
        }
    }
};
