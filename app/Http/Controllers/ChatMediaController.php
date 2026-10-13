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
 * Chat attachment uploads (images, files, voice notes, videos).
 *
 * Routing by type:
 *  - voice notes, images, files → local public disk under storage/app/public/chat-media/{convId}/
 *  - videos                     → Cloudflare R2 disk (configured in config/filesystems.php as 'r2')
 *                                 + a client-captured JPEG thumbnail, also on R2
 *
 * We route videos to R2 for two reasons: (1) they're the only chat
 * attachments that commonly cross tens of MB, so keeping them off the
 * app server avoids filling up the VPS disk; (2) R2 serves them with
 * byte-range support and no egress fees, so inline <video> playback is
 * cheap and smooth. Everything else stays local because the per-message
 * cost of a round-trip to R2 isn't worth it for a 50 KB voice note.
 */
class ChatMediaController extends Controller
{
    /** Hard cap on video uploads. Matches the client-side check in chat.blade.php. */
    private const VIDEO_MAX_BYTES = 100 * 1024 * 1024; // 100 MB

    /** Non-video chat uploads (images, docs, voice). */
    private const DEFAULT_MAX_BYTES = 20 * 1024 * 1024; // 20 MB

    public function uploadVoice(Request $request)
    {
        return $this->handleUpload($request, 'voice_note', 'audio', true);
    }

    public function uploadAttachment(Request $request)
    {
        return $this->handleUpload($request, 'attachment', null, false);
    }

    /**
     * Shared upload handler. For video uploads the client also POSTs a
     * `thumbnail` image (JPEG captured from the first readable frame)
     * which we store next to the video on R2 and reference as the
     * <video poster=""> on the chat side.
     */
    protected function handleUpload(Request $request, string $field, ?string $forceType, bool $isVoice)
    {
        abort_unless($request->user(), 401);

        // We validate the file size per-type AFTER we know the MIME, since
        // videos get a bigger allowance than the default 20 MB.
        $rules = [
            'conversation_id' => 'required|integer|exists:conversations,id',
            $field            => 'required|file',
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

        $mime = $upload->getMimeType() ?: 'application/octet-stream';
        $type = $forceType ?: $this->detectType($mime);
        $size = $upload->getSize();

        // Per-type size cap. Video gets 100 MB (R2); everything else keeps
        // the previous 20 MB cap (local disk).
        $cap = $type === 'video' ? self::VIDEO_MAX_BYTES : self::DEFAULT_MAX_BYTES;
        if ($size !== false && $size > $cap) {
            $capMb = (int) ($cap / 1024 / 1024);
            return response()->json([
                'error'  => 'too_large',
                'detail' => "File is larger than {$capMb} MB.",
            ], 413);
        }

        try {
            if ($type === 'video') {
                return $this->storeVideo($request, $conversation, $user, $upload, $mime, $size);
            }
            return $this->storeOnLocalDisk($conversation, $user, $upload, $mime, $type, $isVoice, (int)($data['duration'] ?? 0));
        } catch (\Throwable $e) {
            Log::error('Media upload failed', [
                'type'  => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'storage failed', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Legacy path — images, docs, voice notes. Stored on the local
     * "public" disk under storage/app/public/chat-media/{convId}/.
     * Behaviour unchanged from before R2 was added so existing rows
     * (and the voice-recorder flow) keep working.
     */
    protected function storeOnLocalDisk(Conversation $conversation, $user, $upload, string $mime, string $type, bool $isVoice, int $duration)
    {
        if ($isVoice) {
            $ext = str_contains($mime, 'mp4') ? 'm4a' : 'webm';
            $filename = 'voice-' . Str::random(24) . '.' . $ext;
            $originalName = $filename;
        } else {
            $ext = $upload->getClientOriginalExtension() ?: $upload->guessExtension() ?: 'bin';
            $filename = Str::random(24) . '.' . strtolower($ext);
            $originalName = $upload->getClientOriginalName() ?: $filename;
        }

        $dir = "chat-media/{$conversation->id}";
        $absTargetDir = storage_path('app/public/' . $dir);
        if (!is_dir($absTargetDir)) {
            @mkdir($absTargetDir, 0755, true);
        }
        $upload->move($absTargetDir, $filename);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message'         => '',
            'status'          => 'sent',
            'attachment_path' => "{$dir}/{$filename}",
            'attachment_disk' => 'public',
            'attachment_type' => $type,
            'attachment_mime' => $mime,
            'attachment_size' => filesize($absTargetDir . DIRECTORY_SEPARATOR . $filename) ?: null,
            'attachment_duration' => $isVoice ? max(1, $duration) : null,
            'attachment_original_name' => $originalName,
            'expires_at' => Message::ttlToExpiry($user->pref_disappearing_default ?? 'never'),
        ]);

        $this->finaliseMessage($conversation, $user, $message);

        return response()->json([
            'ok'         => true,
            'message_id' => $message->id,
            'url'        => route('chat.media.serve', $message->id),
        ]);
    }

    /**
     * Video path — upload to Cloudflare R2 along with the client-captured
     * thumbnail. If R2 isn't configured (missing env vars), fall back to
     * local disk so the feature still works in dev without creds.
     */
    protected function storeVideo(Request $request, Conversation $conversation, $user, $upload, string $mime, $size)
    {
        $disk = $this->r2Available() ? 'r2' : 'public';

        $ext = $upload->getClientOriginalExtension() ?: $upload->guessExtension() ?: 'mp4';
        $filename = Str::random(24) . '.' . strtolower($ext);
        $originalName = $upload->getClientOriginalName() ?: $filename;
        $dir = "chat-media/{$conversation->id}";
        $key = "{$dir}/{$filename}";

        // Stream the uploaded file to the target disk. For 'public' the
        // Storage::putFileAs call writes under storage/app/public; for
        // 'r2' it pushes straight to the bucket.
        $stream = fopen($upload->getRealPath(), 'rb');
        try {
            Storage::disk($disk)->put($key, $stream, [
                'ContentType' => $mime,
                'visibility'  => 'private',
            ]);
        } finally {
            if (is_resource($stream)) fclose($stream);
        }

        // Client-captured thumbnail is optional — if the browser couldn't
        // decode a frame (rare), we just skip the poster and the video
        // tag will show a black box until play.
        $thumbKey = null;
        $thumbnail = $request->file('thumbnail');
        if ($thumbnail && $thumbnail->isValid() && str_starts_with((string) $thumbnail->getMimeType(), 'image/')) {
            $thumbExt = $thumbnail->getClientOriginalExtension() ?: 'jpg';
            $thumbKey = "{$dir}/" . Str::random(24) . '-thumb.' . strtolower($thumbExt);
            $tstream = fopen($thumbnail->getRealPath(), 'rb');
            try {
                Storage::disk($disk)->put($thumbKey, $tstream, [
                    'ContentType' => $thumbnail->getMimeType() ?: 'image/jpeg',
                    'visibility'  => 'private',
                ]);
            } finally {
                if (is_resource($tstream)) fclose($tstream);
            }
        }

        // Client sends optional duration (seconds, from <video>.duration)
        // so the chat row shows it under the player without having to
        // decode server-side.
        $duration = (int) $request->input('duration', 0);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'message'         => '',
            'status'          => 'sent',
            'attachment_path' => $key,
            'attachment_disk' => $disk,
            'attachment_thumbnail_path' => $thumbKey,
            'attachment_type' => 'video',
            'attachment_mime' => $mime,
            'attachment_size' => $size ?: null,
            'attachment_duration' => $duration > 0 ? min($duration, 65535) : null,
            'attachment_original_name' => $originalName,
            'expires_at' => Message::ttlToExpiry($user->pref_disappearing_default ?? 'never'),
        ]);

        $this->finaliseMessage($conversation, $user, $message);

        return response()->json([
            'ok'            => true,
            'message_id'    => $message->id,
            'url'           => route('chat.media.serve', $message->id),
            'thumbnail_url' => $thumbKey ? route('chat.media.thumb', $message->id) : null,
        ]);
    }

    /**
     * Common tail: bump the conversation's last_message_at and fire the
     * NewChatMessage broadcast. Pulled out so both storeOnLocalDisk and
     * storeVideo stay single-purpose.
     */
    protected function finaliseMessage(Conversation $conversation, $user, Message $message): void
    {
        $conversation->update(['last_message_at' => now()]);

        try {
            if ($conversation->is_support) {
                $target = ($user->is_admin ?? false) ? (int) $conversation->user_one_id : 0;
                broadcast(new NewChatMessage($message, $target))->toOthers();
            } else {
                $otherUserId = $conversation->getOtherUserId($user->id);
                if ($otherUserId) {
                    broadcast(new NewChatMessage($message, $otherUserId))->toOthers();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Chat media broadcast failed: ' . $e->getMessage());
        }
    }

    /** True if the r2 disk is configured with at least a key + bucket + endpoint. */
    protected function r2Available(): bool
    {
        return !empty(config('filesystems.disks.r2.key'))
            && !empty(config('filesystems.disks.r2.secret'))
            && !empty(config('filesystems.disks.r2.bucket'))
            && !empty(config('filesystems.disks.r2.endpoint'));
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
     * Stream the attachment, OR redirect to its R2 URL for files stored
     * on the r2 disk. R2 handles range requests + CDN caching far better
     * than streaming through PHP, so we hand the browser straight over.
     */
    public function serve(Request $request, int $messageId)
    {
        $message = $this->findAccessibleMessage($request, $messageId);
        return $this->serveStoredFile(
            $message->attachment_disk ?: 'public',
            $message->attachment_path,
            $message->attachment_mime ?: 'application/octet-stream'
        );
    }

    /**
     * Serve the video poster thumbnail (R2-only path — thumbnails only
     * exist for video uploads, which always go to R2 when configured).
     */
    public function serveThumbnail(Request $request, int $messageId)
    {
        $message = $this->findAccessibleMessage($request, $messageId);
        if (!$message->attachment_thumbnail_path) abort(404);
        return $this->serveStoredFile(
            $message->attachment_disk ?: 'public',
            $message->attachment_thumbnail_path,
            'image/jpeg'
        );
    }

    /** Shared permission check for serve + serveThumbnail. */
    protected function findAccessibleMessage(Request $request, int $messageId): Message
    {
        $user = $request->user();
        abort_unless($user, 401);

        $message = Message::with('conversation')->find($messageId);
        if (!$message || !$message->attachment_path) abort(404);

        $conversation = $message->conversation;
        $isParticipant = $conversation && $conversation->hasUser($user->id);
        $isAdminOnSupport = ($user->is_admin ?? false) && $conversation && $conversation->is_support;
        if (!$isParticipant && !$isAdminOnSupport) abort(403);

        return $message;
    }

    /**
     * For local-disk paths: stream the file through PHP with Accept-Ranges
     * so the browser can seek. For R2 paths: 302 to the public URL if the
     * bucket has one configured, otherwise mint a short-lived presigned
     * URL. Either way the browser gets to use HTTP range + CDN caching
     * without PHP touching the bytes.
     */
    protected function serveStoredFile(string $disk, string $path, string $mime)
    {
        $mime = preg_replace('/;.*$/', '', $mime); // Chrome ignores codecs=; strip

        if ($disk === 'r2') {
            try {
                $r2 = Storage::disk('r2');
                $publicBase = config('filesystems.disks.r2.url');
                if ($publicBase) {
                    return redirect()->away(rtrim($publicBase, '/') . '/' . ltrim($path, '/'));
                }
                // 10-min signed URL — long enough for the browser to
                // seek around, short enough that scraped links expire.
                return redirect()->away($r2->temporaryUrl($path, now()->addMinutes(10)));
            } catch (\Throwable $e) {
                Log::error('R2 serve failed', ['path' => $path, 'error' => $e->getMessage()]);
                abort(500);
            }
        }

        // Local public disk — stream with range support.
        $abs = storage_path('app/public/' . $path);
        if (!is_file($abs)) abort(404);
        return response()->file($abs, [
            'Content-Type'  => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
