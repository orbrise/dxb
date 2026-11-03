<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_bots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            // Which admin workflow this bot plugs into. 'support_chat' = draft
            // replies in the support inbox. Future tasks (broadcasts, profile
            // moderation, …) just add new enum values; no schema change.
            $table->string('task', 32)->default('support_chat')->index();
            $table->text('system_prompt');
            $table->longText('knowledge')->nullable();
            $table->string('model', 64)->default('grok-4-fast');
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_bots');
    }
};
