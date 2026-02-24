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
            $table->integer('package_days')->nullable()->after('package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_profiles', function (Blueprint $table) {
            $table->dropColumn('package_days');
        });
    }
};
