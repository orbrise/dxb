<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'is_support')) {
                $table->boolean('is_support')->default(false)->after('user_two_id');
                $table->index('is_support');
            }
            if (!Schema::hasColumn('conversations', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('is_support');
            }
        });

        // Make user_two_id nullable for support conversations (support convos
        // have only the customer as user_one_id; any admin can reply).
        // Guarded by a doctrine/dbal check via raw SQL to keep this migration
        // independent of that package being installed.
        try {
            DB::statement('ALTER TABLE conversations MODIFY user_two_id BIGINT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // Non-fatal — column may already be nullable, or DB not MySQL.
        }
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (Schema::hasColumn('conversations', 'is_pinned')) {
                $table->dropColumn('is_pinned');
            }
            if (Schema::hasColumn('conversations', 'is_support')) {
                $table->dropIndex(['is_support']);
                $table->dropColumn('is_support');
            }
        });

        try {
            DB::statement('ALTER TABLE conversations MODIFY user_two_id BIGINT UNSIGNED NOT NULL');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
