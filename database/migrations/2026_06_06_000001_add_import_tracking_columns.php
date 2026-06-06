<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('imported_user_id')->nullable()->after('scraped_at');
            $table->unsignedBigInteger('imported_profile_id')->nullable()->after('imported_user_id');
            $table->timestamp('imported_at')->nullable()->after('imported_profile_id');
            $table->string('source_city')->nullable()->after('imported_at');
            $table->index('imported_user_id');
            $table->index('source_city');
        });

        Schema::table('users_profiles', function (Blueprint $table) {
            $table->string('imported_from')->nullable()->after('archive_reason');
            $table->string('imported_external_id')->nullable()->after('imported_from');
            $table->index('imported_from');
            $table->index('imported_external_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('imported_from')->nullable()->after('status');
            $table->index('imported_from');
        });
    }

    public function down(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            $table->dropIndex(['imported_user_id']);
            $table->dropIndex(['source_city']);
            $table->dropColumn(['imported_user_id', 'imported_profile_id', 'imported_at', 'source_city']);
        });

        Schema::table('users_profiles', function (Blueprint $table) {
            $table->dropIndex(['imported_from']);
            $table->dropIndex(['imported_external_id']);
            $table->dropColumn(['imported_from', 'imported_external_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['imported_from']);
            $table->dropColumn('imported_from');
        });
    }
};
