<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Dedicated upload endpoint for chat voice notes.
 * Bypasses Livewire's programmatic file-upload API (which was returning
 * "Path cannot be empty" errors for MediaRecorder blobs) — the browser
 * POSTs the audio Blob here as multipart/form-data and we handle
 * storage + message creation + broadcast ourselves.
 */
class ChatMediaController extends Controller
{
    public function uploadVoice(Request $request)
    {
        return $this->handleUpload($request, 'voice_note', 'audio', true);
    }

    /**
     * General attachment upload — images, files, videos. Same permission
     * model as uploadVoice; we route the image/file paperclip flow through
     * here (instead of Livewire's wire:model file upload) because Livewire's
     * temp-upload storage also hits the putFileAs "Path cannot be empty"
     * bug on this Windows/Laragon install.
     */
    public function uploadAttachment(Request $request)
    {
        return $this->handleUpload($request, 'attachment', null, false);
    }

    /**
     * Shared upload handler. If $forceType is null we detect from MIME.
     * If $isVoice we accept a duration payload and mark the row as audio.
     */
    protected function handleUpload(Request $request, string $field, ?string $forceType, bool $isVoice)
    {
        abort_unless($request->user(), 401);

        $rules = [
            'conversation_id' => 'required|integer|exists:conversations,id',
            $field            => 'required|file|max:20480', // 20 MB
        ];
        if ($isVoice) {
            $rules['duration'] = 'nullable|integer|min:1|max:600';
        }
        $data = $request->validate($rules);

        $user = $request->user();
        $userId = $user->id;
        $conversation = Conversation::find($data['conversation_id']);

        if (!$conversation) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $isParticipant = $conversation->hasUser($userId);
        $isAdminOnSupport = ($user->is_admin ?? false) && $conversation->is_support;
        if (!$isParticipant && !$isAdminOnSupport) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $upload = $request->file($field);
        if (!$upload || !$upload->isValid()) {
            return response()->json(['error' => 'invalid upload'], 422);
        }

        try {
            // Diagnostic — on some Windows/Laragon setups
            // UploadedFile::getRealPath() returns false, which cascades into a
            // "Path cannot be empty" from Flysystem via putFileAs. Log the
            // observed values so we can see it in laravel.log if this recurs.
            Log::info('Voice upload diag', [
                'client_name' => $upload->getClientOriginalName(),
                'client_mime' => $upload->getClientMimeType(),
                'client_size' => $upload->getSize(),
                'real_path'   => $upload->getRealPath(),
                'path_name'   => $upload->getPathname(),
                'error'       => $upload->getError(),
            ]);

            $mime = $upload->getMimeType() ?: 'application/octet-stream';

            // Pick extension. For voice: normalize to .webm/.m4a. For files:
            // prefer the client extension, fall back to a guess.
            if ($isVoice) {
                $ext = str_contains($mime, 'mp4') ? 'm4a' : 'webm';
                $filename = 'voice-' . Str::random(24) . '.' . $ext;
                $originalName = $filename;
            } else {
                $ext = $upload->getClientOriginalExtension() ?: $upload->guessExtension() ?: 'bin';
                $filename = Str::random(24) . '.' . strtolower($ext);
                $originalName = $upload->getClientOriginalName() ?: $filename;
            }

            $type = $forceType ?: $this->detectType($mime);

            $dir = "chat-media/{$conversation->id}";
            $absTargetDir = storage_path('app/public/' . $dir);
            if (!is_dir($absTargetDir)) {
                @mkdir($absTargetDir, 0755, true);
            }
            $upload->move($absTargetDir, $filename);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'message' => '',
                'status' => 'sent',
                'attachment_path' => "{$dir}/{$filename}",
                'attachment_type' => $type,
                'attachment_mime' => $mime,
                'attachment_size' => filesize($absTargetDir . DIRECTORY_SEPARATOR . $filename) ?: null,
                'attachment_duration' => $isVoice ? ((int) ($data['duration'] ?? 1)) : null,
                'attachment_original_name' => $originalName,
            ]);

            $conversation->update(['last_message_at' => now()]);

            try {
                if ($conversation->is_support) {
                    // Admin replying → target the customer (user_one_id) so
                    // both the customer's chat channel and the support-inbox
                    // channel receive the broadcast.
                    // Customer sending → target 0 so only the shared
                    // support-inbox channel gets it.
                    $target = ($user->is_admin ?? false) ? (int) $conversation->user_one_id : 0;
                    broadcast(new NewChatMessage($message, $target))->toOthers();
                } else {
                    $otherUserId = $conversation->getOtherUserId($userId);
                    if ($otherUserId) {
                        broadcast(new NewChatMessage($message, $otherUserId))->toOthers();
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Voice upload broadcast failed: ' . $e->getMessage());
            }

            return response()->json([
                'ok' => true,
                'message_id' => $message->id,
                'url' => route('chat.media.serve', $message->id),
            ]);
        } catch (\Throwable $e) {
            Log::error('Media upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'storage failed', 'detail' => $e->getMessage()], 500);
        }
    }

    protected function detectType(?string $mime): string
    {
        if (!$mime) return 'file';
        if (str_starts_with($mime, 'image/')) return 'image';
        if (str_starts_with($mime, 'audio/')) return 'audio';
        if (str_starts_with($mime, 'video/')) return 'video';
        return 'file';
    }

    /**
     * Stream a chat message's attachment with the correct Content-Type.
     * Only participants of the conversation (or admins on Support convos)
     * may access it. This is a robust fallback for cases where the web
     * server hasn't mapped .webm / .opus in its MIME table.
     */
    public function serve(Request $request, int $messageId)
    {
        $user = $request->user();
        abort_unless($user, 401);

        $message = Message::with('conversation')->find($messageId);
        if (!$message || !$message->attachment_path) {
            abort(404);
        }

        $conversation = $message->conversation;
        $isParticipant = $conversation && $conversation->hasUser($user->id);
        $isAdminOnSupport = ($user->is_admin ?? false) && $conversation && $conversation->is_support;
        if (!$isParticipant && !$isAdminOnSupport) {
            abort(403);
        }

        $abs = storage_path('app/public/' . $message->attachment_path);
        if (!is_file($abs)) {
            abort(404);
        }

        $mime = $message->attachment_mime ?: 'application/octet-stream';
        // Chrome ignores codecs= in Content-Type; strip if present.
        $mime = preg_replace('/;.*$/', '', $mime);

        return response()->file($abs, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
