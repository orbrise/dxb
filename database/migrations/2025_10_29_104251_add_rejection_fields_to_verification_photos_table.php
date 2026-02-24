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
            if (!Schema::hasColumn('verification_photos', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('verification_photos', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('verification_photos', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_photos', function (Blueprint $table) {
            if (Schema::hasColumn('verification_photos', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
            if (Schema::hasColumn('verification_photos', 'verified_at')) {
                $table->dropColumn('verified_at');
            }
            if (Schema::hasColumn('verification_photos', 'verified_by')) {
                $table->dropColumn('verified_by');
            }
        });
    }
};
