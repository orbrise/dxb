<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * batch_id groups the N messages that came from a single multi-image send
 * so the chat UI can render them as one WhatsApp-style album grid instead
 * of N stacked bubbles. One UUID per batch, generated client-side when
 * the preview modal's Send button is tapped and passed through
 * ChatMediaController::storeImage with every upload in that batch.
 *
 * Nullable because 99 % of messages (text, single image, video, voice,
 * calls, legacy rows) are not part of a batch and should render as-is.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'batch_id')) {
                $table->string('batch_id', 36)->nullable()->after('attachment_original_name');
                // Index lets the chat loader fetch "all messages in batch X"
                // efficiently if we ever need it; the current grouper walks
                // the already-loaded thread so this is future-proofing.
                $table->index('batch_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'batch_id')) {
                $table->dropIndex(['batch_id']);
                $table->dropColumn('batch_id');
            }
        });
    }
};
