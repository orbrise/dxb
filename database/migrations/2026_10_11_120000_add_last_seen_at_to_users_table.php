<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add last_seen_at so the chat header can show WhatsApp-style presence
 * ("online" / "last seen 5 minutes ago") instead of just the user's email.
 *
 * UpdateLastSeen middleware writes to this column — debounced to once per
 * minute per user via a cache guard, so it doesn't flood the DB on every
 * request.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('remember_token');
                $table->index('last_seen_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_seen_at')) {
                $table->dropIndex(['last_seen_at']);
                $table->dropColumn('last_seen_at');
            }
        });
    }
};
