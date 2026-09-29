<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Performance indexes for the chat feature.
 *
 * The queries these back:
 *   - markMessagesAsDelivered: WHERE sender_id != X AND status = 'sent'
 *                              → messages(sender_id, status)
 *   - unread-count aggregation: WHERE conversation_id IN (...)
 *                               AND sender_id != X AND status IN (...)
 *                               → messages(conversation_id, sender_id, status)
 *   - getGalleryMedia:        WHERE conversation_id IN (...)
 *                             AND attachment_type IN ('image','video')
 *                             AND attachment_path IS NOT NULL
 *                             ORDER BY created_at DESC
 *                             → messages(conversation_id, attachment_type, created_at)
 *   - active() scope:         WHERE expires_at IS NULL OR expires_at > NOW()
 *                             → messages(expires_at)
 *   - StatusView batch lookup: WHERE status_id IN (...) AND viewer_id = X
 *                              → status_views(status_id, viewer_id)
 *
 * Without these, prod DBs with real message counts (10k+ rows) do full
 * table scans on every Livewire action, turning sub-second local
 * responses into 3-6 second prod responses. Each Livewire click runs
 * render() which touches every one of these queries at least once.
 *
 * hasIndex helper — MySQL only. Skips creation if the index already
 * exists so re-running is safe. Sqlite/Pgsql users should adapt.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!$this->indexExists('messages', 'messages_sender_id_status_index')) {
                $table->index(['sender_id', 'status'], 'messages_sender_id_status_index');
            }
            if (!$this->indexExists('messages', 'messages_conv_sender_status_index')) {
                $table->index(['conversation_id', 'sender_id', 'status'], 'messages_conv_sender_status_index');
            }
            if (!$this->indexExists('messages', 'messages_conv_atttype_created_index')) {
                $table->index(['conversation_id', 'attachment_type', 'created_at'], 'messages_conv_atttype_created_index');
            }
            if (!$this->indexExists('messages', 'messages_expires_at_index')) {
                $table->index('expires_at', 'messages_expires_at_index');
            }
        });

        Schema::table('status_views', function (Blueprint $table) {
            if (!$this->indexExists('status_views', 'status_views_status_viewer_index')) {
                $table->index(['status_id', 'viewer_id'], 'status_views_status_viewer_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_sender_id_status_index');
            $table->dropIndex('messages_conv_sender_status_index');
            $table->dropIndex('messages_conv_atttype_created_index');
            $table->dropIndex('messages_expires_at_index');
        });

        Schema::table('status_views', function (Blueprint $table) {
            $table->dropIndex('status_views_status_viewer_index');
        });
    }

    /**
     * Portable check across MySQL / MariaDB. Uses information_schema
     * which works on both. Returns false silently on drivers that
     * don't expose it — the migration will just try to create and
     * fail gracefully if the index somehow already exists.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $connection = DB::connection();
            $database = $connection->getDatabaseName();
            $count = $connection->selectOne(
                "SELECT COUNT(*) as c FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$database, $table, $indexName]
            );
            return (int) ($count->c ?? 0) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
