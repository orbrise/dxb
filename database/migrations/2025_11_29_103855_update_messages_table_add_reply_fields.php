<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('messages', 'reply')) {
                $table->text('reply')->nullable()->after('message');
            }
            if (!Schema::hasColumn('messages', 'status')) {
                $table->string('status')->nullable()->default('unread')->after('reply');
            }
            if (!Schema::hasColumn('messages', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'reply')) {
                $table->dropColumn('reply');
            }
            if (Schema::hasColumn('messages', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('messages', 'replied_at')) {
                $table->dropColumn('replied_at');
            }
        });
    }
};
