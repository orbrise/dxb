<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'calls_blocked')) {
                $table->boolean('calls_blocked')->default(false)->after('pref_disappearing_default');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'calls_blocked')) {
                $table->dropColumn('calls_blocked');
            }
        });
    }
};
