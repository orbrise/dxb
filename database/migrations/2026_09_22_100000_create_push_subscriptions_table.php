<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // Push endpoint is the URL the browser gave us — the same user
            // can have several (one per browser / device). Kept unique so
            // repeat subscribes from the same browser upsert cleanly.
            $table->string('endpoint', 512)->unique();
            $table->string('p256dh_key', 255);
            $table->string('auth_key', 100);
            $table->string('content_encoding', 20)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
