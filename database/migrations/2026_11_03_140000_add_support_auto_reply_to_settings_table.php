<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'support_auto_reply_enabled')) {
                $table->boolean('support_auto_reply_enabled')->default(false);
            }
            // Minutes a customer waits without an agent reply before the
            // auto reply goes out (see App\Services\SupportAutoReply).
            if (!Schema::hasColumn('settings', 'support_auto_reply_delay')) {
                $table->unsignedSmallInteger('support_auto_reply_delay')->default(5);
            }
            if (!Schema::hasColumn('settings', 'support_auto_reply_message')) {
                $table->text('support_auto_reply_message')->nullable();
            }
            // Admin account the auto reply is sent from — whoever last saved
            // the setting. Customers only ever see it as "Support".
            if (!Schema::hasColumn('settings', 'support_auto_reply_sender_id')) {
                $table->unsignedBigInteger('support_auto_reply_sender_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['support_auto_reply_enabled', 'support_auto_reply_delay', 'support_auto_reply_message', 'support_auto_reply_sender_id'],
            fn ($column) => Schema::hasColumn('settings', $column)
        ));

        if ($columns) {
            Schema::table('settings', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
