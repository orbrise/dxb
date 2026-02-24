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
        Schema::table('seo_keywords', function (Blueprint $table) {
            // Add gender_id column
            $table->unsignedBigInteger('gender_id')->nullable()->after('page');
            
            // Drop service_id column
            $table->dropColumn('service_id');
            
            // Add index for better performance
            $table->index(['gender_id', 'city_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_keywords', function (Blueprint $table) {
            // Add service_id column back
            $table->unsignedBigInteger('service_id')->nullable()->after('page');
            
            // Drop gender_id column
            $table->dropIndex(['gender_id', 'city_id', 'country_id']);
            $table->dropColumn('gender_id');
            
            // Re-add service index
            $table->index(['service_id', 'city_id', 'country_id']);
        });
    }
};
