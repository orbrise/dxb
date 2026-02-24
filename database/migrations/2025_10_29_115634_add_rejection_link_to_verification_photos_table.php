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
        Schema::table('verification_photos', function (Blueprint $table) {
            if (!Schema::hasColumn('verification_photos', 'rejection_link')) {
                $table->string('rejection_link')->nullable()->after('rejection_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_photos', function (Blueprint $table) {
            if (Schema::hasColumn('verification_photos', 'rejection_link')) {
                $table->dropColumn('rejection_link');
            }
        });
    }
};
