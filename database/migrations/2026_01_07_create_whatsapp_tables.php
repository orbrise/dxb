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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->index('phone_number');
            $table->index('last_message_at');
        });
        
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->onDelete('cascade');
            $table->text('message');
            $table->enum('direction', ['incoming', 'outgoing'])->default('outgoing');
            $table->string('status')->default('sent'); // sent, delivered, read, failed
            $table->string('whatsapp_message_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            
            $table->index('conversation_id');
            $table->index('direction');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_conversations');
    }
};
