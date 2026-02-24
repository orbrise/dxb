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
        Schema::create('default_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Setting name like 'global', 'homepage', 'escorts', etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Higher priority = used first
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_seo_settings');
    }
};
