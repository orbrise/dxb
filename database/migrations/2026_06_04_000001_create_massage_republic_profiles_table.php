<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('massage_republic_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('profile_url')->unique();
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('gender')->nullable();
            $table->string('rating')->nullable();
            $table->text('services')->nullable();
            $table->text('description')->nullable();
            $table->json('image_urls')->nullable();
            $table->longText('raw_html')->nullable();
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('massage_republic_profiles');
    }
};
 