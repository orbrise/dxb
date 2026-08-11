<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ivysociete_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('profile_url')->unique();
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->text('description')->nullable();
            $table->json('image_urls')->nullable();
            $table->json('attributes')->nullable();
            $table->decimal('incall_price', 10, 2)->nullable();
            $table->decimal('outcall_price', 10, 2)->nullable();
            $table->string('incall_currency', 8)->nullable();
            $table->string('outcall_currency', 8)->nullable();
            $table->timestamp('scraped_at')->nullable();
            $table->unsignedBigInteger('imported_user_id')->nullable();
            $table->unsignedBigInteger('imported_profile_id')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->string('source_city')->nullable();
            $table->timestamps();

            $table->index('imported_user_id');
            $table->index('source_city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ivysociete_profiles');
    }
};
