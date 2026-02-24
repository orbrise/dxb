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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_one_id'); // Always the lower user_id
            $table->unsignedBigInteger('user_two_id'); // Always the higher user_id
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Ensure unique conversation between two users
            $table->unique(['user_one_id', 'user_two_id']);
            
            // Foreign keys
            $table->foreign('user_one_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for faster lookups
            $table->index(['user_one_id', 'last_message_at']);
            $table->index(['user_two_id', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
