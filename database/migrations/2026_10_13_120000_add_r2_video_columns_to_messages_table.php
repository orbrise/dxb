<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds two columns needed for the Cloudflare-R2-backed video upload flow:
 *  - attachment_disk: which filesystem disk holds the file. Legacy rows
 *    (voice notes, images uploaded before R2 was wired up) live on the
 *    local "public" disk; new video uploads go to "r2". serve() uses this
 *    to decide between streaming the file and redirecting to a presigned
 *    R2 URL.
 *  - attachment_thumbnail_path: for video attachments only, a path (same
 *    disk as attachment_path) to a JPEG poster captured client-side from
 *    the first readable frame. Used as the <video poster=""> so the chat
 *    thread shows a frame instead of a black box before playback.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'attachment_disk')) {
                $table->string('attachment_disk', 16)->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('messages', 'attachment_thumbnail_path')) {
                $table->string('attachment_thumbnail_path')->nullable()->after('attachment_disk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            foreach (['attachment_disk', 'attachment_thumbnail_path'] as $col) {
                if (Schema::hasColumn('messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
