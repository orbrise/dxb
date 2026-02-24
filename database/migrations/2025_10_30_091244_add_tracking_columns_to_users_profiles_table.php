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
            $table->unsignedBigInteger('profile_views')->default(0)->after('is_active');
            $table->unsignedBigInteger('phone_clicks')->default(0)->after('profile_views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_profiles', function (Blueprint $table) {
            $table->dropColumn(['profile_views', 'phone_clicks']);
        });
    }
};
