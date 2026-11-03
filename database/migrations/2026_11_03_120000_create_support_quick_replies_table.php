<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // WhatsApp Business-style quick replies, shared by every support agent.
        // Agents type "/{shortcut}" in the support inbox composer to insert one.
        Schema::create('support_quick_replies', function (Blueprint $table) {
            $table->id();
            $table->string('shortcut', 30)->unique();
            $table->text('message');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_quick_replies');
    }
};
