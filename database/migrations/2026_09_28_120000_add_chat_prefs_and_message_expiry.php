<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pref_show_unanswered')) {
                $table->boolean('pref_show_unanswered')->default(false)->after('is_private');
            }
            if (!Schema::hasColumn('users', 'pref_disappearing_default')) {
                // 'never' | '24h' | '1w' | '1m'
                $table->string('pref_disappearing_default', 10)->default('never')->after('pref_show_unanswered');
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('created_at');
                $table->index('expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'expires_at')) {
                $table->dropIndex(['expires_at']);
                $table->dropColumn('expires_at');
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pref_disappearing_default')) {
                $table->dropColumn('pref_disappearing_default');
            }
            if (Schema::hasColumn('users', 'pref_show_unanswered')) {
                $table->dropColumn('pref_show_unanswered');
            }
        });
    }
};
