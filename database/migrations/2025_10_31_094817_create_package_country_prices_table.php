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
        Schema::create('package_country_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('package_id')->index();
            $table->integer('country_id')->index();
            $table->json('price_tiers'); // Store country-specific price tiers
            $table->timestamps();
            
            // Ensure unique package-country combination
            $table->unique(['package_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_country_prices');
    }
};
