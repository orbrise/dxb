<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Archive chat" in the admin Support Inbox: moves a support chat from the
 * All / Unread filters to Archived, for admins only. It comes back to All
 * once the customer writes again (Conversation::scopeArchivedInSupportInbox).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'inbox_archived_at')) {
                $table->timestamp('inbox_archived_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'inbox_archived_at')) {
                $table->dropColumn('inbox_archived_at');
            }
        });
    }
};
