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
        Schema::table('users_profiles', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('video');
            $table->string('ip_country', 100)->nullable()->after('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_profiles', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'ip_country']);
        });
    }
};
