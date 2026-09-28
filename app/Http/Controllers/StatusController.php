<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\StatusView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Handles uploading, deleting, and view-marking of WhatsApp-style
 * ephemeral statuses (photo / video / text). Statuses auto-expire 24 h
 * after posting via the Status::active() scope on the read side; there
 * is no cron for hard deletion.
 */
class StatusController extends Controller
{
    /**
     * Upload a photo or video status. The client POSTs multipart/form-data
     * with 'media' (file) and optional 'content' (caption). Detects type
     * from MIME. Same putFileAs workaround as ChatMediaController — we
     * use $upload->move() to sidestep the Windows/Laragon Flysystem bug.
     */
    public function uploadMedia(Request $request)
    {
        abort_unless($request->user(), 401);

        $data = $request->validate([
            'media'   => 'required|file|max:30720', // 30 MB — videos up to ~15s
            'content' => 'nullable|string|max:500',
        ]);

        $upload = $request->file('media');
        if (!$upload || !$upload->isValid()) {
            return response()->json(['error' => 'invalid upload'], 422);
        }

        try {
            $userId = $request->user()->id;
            $mime = $upload->getMimeType() ?: 'application/octet-stream';
            $type = str_starts_with($mime, 'video/') ? 'video' : 'photo';

            $ext = $upload->getClientOriginalExtension() ?: $upload->guessExtension() ?: 'bin';
            $filename = 'st-' . Str::random(24) . '.' . strtolower($ext);

            $dir = "statuses/{$userId}";
            $absDir = storage_path('app/public/' . $dir);
            if (!is_dir($absDir)) {
                @mkdir($absDir, 0755, true);
            }
            $upload->move($absDir, $filename);

            $status = Status::create([
                'user_id'    => $userId,
                'type'       => $type,
                'media_path' => "{$dir}/{$filename}",
                'media_mime' => $mime,
                'media_size' => filesize($absDir . DIRECTORY_SEPARATOR . $filename) ?: null,
                'content'    => $data['content'] ?? null,
            ]);

            return response()->json([
                'ok'        => true,
                'status_id' => $status->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Status upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'storage failed', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a text status — no upload, just content + background color.
     * Uses Livewire's postTextStatus() method instead of this endpoint in
     * the current UI, but kept here as a REST alternative.
     */
    public function storeText(Request $request)
    {
        abort_unless($request->user(), 401);

        $data = $request->validate([
            'content'          => 'required|string|max:500',
            'background_color' => 'nullable|string|max:20',
            'text_color'       => 'nullable|string|max:20',
        ]);

        $status = Status::create([
            'user_id'          => $request->user()->id,
            'type'             => 'text',
            'content'          => $data['content'],
            'background_color' => $data['background_color'] ?? '#075E54',
            'text_color'       => $data['text_color'] ?? '#ffffff',
        ]);

        return response()->json(['ok' => true, 'status_id' => $status->id]);
    }

    /**
     * Mark a status as viewed by the current user. Idempotent — the
     * unique (status_id, viewer_id) index prevents duplicates.
     */
    public function markViewed(Request $request, int $statusId)
    {
        abort_unless($request->user(), 401);

        $status = Status::find($statusId);
        if (!$status || $status->isExpired()) {
            return response()->json(['error' => 'not found'], 404);
        }

        // Don't record own-views (the poster looking at their own status).
        if ($status->user_id === $request->user()->id) {
            return response()->json(['ok' => true, 'self' => true]);
        }

        StatusView::firstOrCreate(
            ['status_id' => $statusId, 'viewer_id' => $request->user()->id],
            ['viewed_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a status you own. Cleans up the media file too.
     */
    public function destroy(Request $request, int $statusId)
    {
        abort_unless($request->user(), 401);

        $status = Status::find($statusId);
        if (!$status) {
            return response()->json(['error' => 'not found'], 404);
        }
        if ($status->user_id !== $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        if ($status->media_path) {
            $abs = storage_path('app/public/' . $status->media_path);
            if (is_file($abs)) @unlink($abs);
        }
        $status->delete();

        return response()->json(['ok' => true]);
    }
}
