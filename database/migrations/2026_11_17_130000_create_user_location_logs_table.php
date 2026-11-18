<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Every place a user was when they registered, logged in, created or edited
 * a profile (admin → User Locations). The users / users_profiles columns
 * keep only the latest of each; this keeps them all.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_location_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('profile_id')->nullable()->index();
            // register | login | profile_create | profile_update
            $table->string('event', 20)->index();
            $table->string('ip', 45)->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });

        // Backfill from what the existing columns already hold.
        DB::statement("
            INSERT INTO user_location_logs (user_id, event, ip, country, created_at)
            SELECT id, 'register', registration_ip, registration_country, created_at
            FROM users WHERE registration_ip IS NOT NULL AND registration_ip <> ''
        ");
        DB::statement("
            INSERT INTO user_location_logs (user_id, profile_id, event, ip, country, created_at)
            SELECT user_id, id, 'profile_create', ip_address, ip_country, created_at
            FROM users_profiles WHERE user_id IS NOT NULL AND ip_address IS NOT NULL AND ip_address <> ''
        ");
        DB::statement("
            INSERT INTO user_location_logs (user_id, event, ip, country, created_at)
            SELECT id, 'login', last_login_ip, last_login_country, last_login_at
            FROM users WHERE last_login_ip IS NOT NULL
        ");
        DB::statement("
            INSERT INTO user_location_logs (user_id, profile_id, event, ip, country, created_at)
            SELECT user_id, id, 'profile_update', updated_ip, updated_country, owner_updated_at
            FROM users_profiles WHERE user_id IS NOT NULL AND updated_ip IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('user_location_logs');
    }
};
