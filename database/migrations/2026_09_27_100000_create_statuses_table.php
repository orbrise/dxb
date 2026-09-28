<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 'photo' | 'video' | 'text'
            $table->string('type', 10);
            // Storage path (photo/video); null for text statuses.
            $table->string('media_path')->nullable();
            $table->string('media_mime', 100)->nullable();
            $table->unsignedInteger('media_size')->nullable();
            // Text content for text statuses; caption for photo/video.
            $table->text('content')->nullable();
            // Text-status background (CSS colour string). WhatsApp-style.
            $table->string('background_color', 20)->nullable();
            $table->string('text_color', 20)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('status_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->constrained()->cascadeOnDelete();
            $table->foreignId('viewer_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('viewed_at')->useCurrent();

            $table->unique(['status_id', 'viewer_id']);
            $table->index('viewer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_views');
        Schema::dropIfExists('statuses');
    }
};
