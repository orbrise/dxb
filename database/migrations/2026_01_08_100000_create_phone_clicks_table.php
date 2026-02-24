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
        Schema::create('phone_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->string('ip_address', 45); // IPv6 support
            $table->string('user_agent')->nullable();
            $table->string('country', 2)->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // If logged in
            $table->timestamps();
            
            // Index for fast lookups
            $table->index(['profile_id', 'ip_address', 'created_at']);
            $table->index('profile_id');
            $table->index('created_at');
            
            // Foreign key
            $table->foreign('profile_id')->references('id')->on('users_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_clicks');
    }
};
