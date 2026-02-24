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
            $table->unsignedBigInteger('service_id')->nullable()->after('page');
            $table->unsignedBigInteger('city_id')->nullable()->after('service_id');
            $table->unsignedBigInteger('country_id')->nullable()->after('city_id');
            $table->text('content')->nullable()->after('description');
            
            // Make page nullable since we now have category/location based SEO
            $table->string('page')->nullable()->change();
            
            // Add index for better performance
            $table->index(['service_id', 'city_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_keywords', function (Blueprint $table) {
            $table->dropIndex(['service_id', 'city_id', 'country_id']);
            $table->dropColumn(['service_id', 'city_id', 'country_id', 'content']);
            $table->string('page')->nullable(false)->change();
        });
    }
};
