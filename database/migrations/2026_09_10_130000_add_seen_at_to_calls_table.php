<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            if (!Schema::hasColumn('calls', 'seen_at')) {
                $table->timestamp('seen_at')->nullable()->after('ended_at');
                // Speeds up the "unseen missed calls" query on the sidebar.
                $table->index(['callee_id', 'status', 'seen_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            if (Schema::hasColumn('calls', 'seen_at')) {
                $table->dropIndex(['callee_id', 'status', 'seen_at']);
                $table->dropColumn('seen_at');
            }
        });
    }
};
