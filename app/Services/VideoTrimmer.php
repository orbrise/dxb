<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

/**
 * Cuts a time range out of an uploaded video with ffmpeg — the fast path for
 * the trim bar in the support inbox's send preview. When available() says
 * ffmpeg can't run, the browser records the clip itself instead (slower: it
 * takes as long as the clip).
 */
class VideoTrimmer
{
    public static function available(): bool
    {
        return self::status()['ok'];
    }

    /**
     * ['ok' => bool, 'reason' => ?string]. The reason is shown in the send
     * preview so whoever runs the server can see why trimming is off. A
     * working ffmpeg is remembered for a day; a failure only for a minute,
     * so fixing the server (installing ffmpeg, allowing proc_open) shows up
     * straight away.
     */
    public static function status(): array
    {
        $cached = Cache::get('video-trimmer.available');
        if (is_array($cached)) {
            return $cached;
        }

        $binary = self::binary();
        $error = null;
        try {
            $result = Process::timeout(10)->run([$binary, '-version']);
            if (!$result->successful()) {
                $error = trim($result->errorOutput()) ?: 'exit code ' . $result->exitCode();
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $status = ['ok' => $error === null, 'reason' => null];
        if ($error !== null) {
            $status['reason'] = mb_substr("{$binary}: {$error}", 0, 300);
            Log::warning('VideoTrimmer: ffmpeg not usable, trim bar hidden', ['reason' => $status['reason']]);
        }

        Cache::put('video-trimmer.available', $status, $status['ok'] ? now()->addDay() : now()->addMinute());

        return $status;
    }

    /**
     * Writes [$start, $end) seconds of $source to a new MP4 and returns its
     * path; the caller deletes it. Re-encodes so the cut is frame-accurate
     * (stream copy can only cut on keyframes) and caps the width at 1280px,
     * which also shrinks phone-camera clips.
     */
    public static function trim(string $source, float $start, float $end): string
    {
        $base = tempnam(sys_get_temp_dir(), 'trim');
        @unlink($base);
        $target = $base . '.mp4';

        $result = Process::timeout(300)->run([
            self::binary(), '-hide_banner', '-loglevel', 'error', '-y',
            '-ss', sprintf('%.3f', $start),
            '-i', $source,
            '-t', sprintf('%.3f', $end - $start),
            '-map', '0:v:0', '-map', '0:a:0?',
            '-vf', "scale='min(1280,iw)':-2",
            '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '23', '-pix_fmt', 'yuv420p',
            '-c:a', 'aac', '-b:a', '128k',
            '-movflags', '+faststart',
            $target,
        ]);

        if (!$result->successful() || !is_file($target) || filesize($target) === 0) {
            @unlink($target);
            throw new RuntimeException('ffmpeg trim failed: ' . trim($result->errorOutput()));
        }

        return $target;
    }

    // Also used by ProfileVideoThumbnail.
    public static function binary(): string
    {
        $binary = (string) config('services.ffmpeg.binary', 'ffmpeg');

        // "~/bin/ffmpeg" in .env: PHP doesn't expand ~ the way a shell does.
        if (str_starts_with($binary, '~/')) {
            $home = getenv('HOME') ?: (function_exists('posix_getpwuid') ? (posix_getpwuid(posix_geteuid())['dir'] ?? '') : '');
            if ($home) {
                $binary = rtrim($home, '/') . substr($binary, 1);
            }
        }

        return $binary;
    }
}
