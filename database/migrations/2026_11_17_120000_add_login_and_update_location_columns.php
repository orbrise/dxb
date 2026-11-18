<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where a user last logged in from, and where a profile was last edited
 * from by its owner. Same IP + country pair as registration_ip /
 * registration_country (users) and ip_address / ip_country (profiles,
 * set at creation).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_login_ip', 45)->nullable()->after('registration_country');
            $table->string('last_login_country')->nullable()->after('last_login_ip');
            $table->timestamp('last_login_at')->nullable()->after('last_login_country');
        });

        Schema::table('users_profiles', function (Blueprint $table) {
            $table->string('updated_ip', 45)->nullable()->after('ip_country');
            $table->string('updated_country')->nullable()->after('updated_ip');
            $table->timestamp('owner_updated_at')->nullable()->after('updated_country');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_login_ip', 'last_login_country', 'last_login_at']);
        });

        Schema::table('users_profiles', function (Blueprint $table) {
            $table->dropColumn(['updated_ip', 'updated_country', 'owner_updated_at']);
        });
    }
};
