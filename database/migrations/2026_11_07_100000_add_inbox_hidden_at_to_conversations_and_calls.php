<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Delete chat" / "Delete call" in the admin Support Inbox only hide the row
 * from admins — a support chat is shared with the customer, who keeps their
 * history. A hidden chat comes back once the customer writes again.
 */
return new class extends Migration {
    public function up(): void
    {
        foreach (['conversations', 'calls'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'inbox_hidden_at')) {
                    $table->timestamp('inbox_hidden_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['conversations', 'calls'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'inbox_hidden_at')) {
                    $table->dropColumn('inbox_hidden_at');
                }
            });
        }
    }
};
