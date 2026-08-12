<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scraper_auto_cities', function (Blueprint $table) {
            $table->id();
            $table->string('source', 40)->index();
            $table->string('city_slug', 80);
            $table->string('city_name', 160)->nullable();
            $table->unsignedInteger('limit_per_run')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['source', 'city_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraper_auto_cities');
    }
};
