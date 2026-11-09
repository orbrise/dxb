<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Chat message a customer gets when an admin tops up their wallet
            // from the Support Inbox. NULL = SupportInbox::DEFAULT_TOPUP_MESSAGE.
            // utf8mb4 explicitly: the template is full of emoji and styled
            // letters, and older tables on cPanel hosts are often utf8mb3.
            if (!Schema::hasColumn('settings', 'support_topup_message')) {
                $table->text('support_topup_message')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'support_topup_message')) {
            Schema::table('settings', fn (Blueprint $table) => $table->dropColumn('support_topup_message'));
        }
    }
};
