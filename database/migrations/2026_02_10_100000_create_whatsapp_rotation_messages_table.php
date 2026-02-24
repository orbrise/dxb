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
        // Table for storing WhatsApp message templates for rotation
        Schema::create('whatsapp_rotation_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message'); // The message template (can include {url} placeholder)
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Order for rotation
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('order');
        });
        
        // Table for tracking which message was last sent to each profile
        Schema::create('whatsapp_profile_message_tracker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id'); // users_profiles.id
            $table->unsignedBigInteger('last_message_id')->nullable(); // whatsapp_rotation_messages.id
            $table->integer('click_count')->default(0);
            $table->timestamps();
            
            $table->foreign('profile_id')
                  ->references('id')
                  ->on('users_profiles')
                  ->onDelete('cascade');
                  
            $table->foreign('last_message_id')
                  ->references('id')
                  ->on('whatsapp_rotation_messages')
                  ->onDelete('set null');
                  
            $table->unique('profile_id');
            $table->index('last_message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_profile_message_tracker');
        Schema::dropIfExists('whatsapp_rotation_messages');
    }
};
