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
        Schema::create('geo_domains', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('country_name', 100);
            $table->string('domain', 255);
            $table->boolean('is_active')->default(false);
            $table->integer('priority')->default(0); // For ordering
            $table->json('settings')->nullable(); // Additional settings
            $table->timestamps();
            
            $table->index(['is_active', 'country_code']);
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geo_domains');
    }
};
