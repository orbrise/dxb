<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Profile videos — mirrors the profile_images table structure so the two
 * media types share the same mental model (per-profile, per-user, with
 * an is_main flag + manual drag-drop ordering). Up to 10 videos per
 * profile is enforced at the Livewire component level (NewProfile +
 * UserProfile), not the DB — a hard DB cap would bite us on edge cases
 * like admin tooling that needs to batch-import.
 *
 * video + video_thumbnail columns both store the FILENAME only (not the
 * full path); the final URL is built as:
 *   https://assets.evoory.com/uservideos/{user_id}/{profile_id}/{filename}
 * matching the existing userimages/ convention. Thumbnails are captured
 * client-side (first frame of the clip) and stored as JPEG alongside the
 * MP4 so the profile grid can render a poster without the browser having
 * to fetch the whole video just to decode frame 0.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('profile_id')->index();
            $table->string('video');                        // e.g. "1735293847123.mp4"
            $table->string('video_thumbnail')->nullable(); // e.g. "1735293847123.jpg"
            $table->string('mime', 50)->nullable();        // "video/mp4" | "video/webm"
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->boolean('is_main')->default(false);
            $table->unsignedInteger('video_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_videos');
    }
};
