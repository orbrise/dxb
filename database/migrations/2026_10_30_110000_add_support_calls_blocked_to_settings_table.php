<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'support_calls_blocked')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('support_calls_blocked')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'support_calls_blocked')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('support_calls_blocked');
            });
        }
    }
};