<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

/**
 * Cuts a time range out of an uploaded video with ffmpeg — backs the trim bar
 * in the support inbox's send preview. The UI only offers trimming when
 * available() says the binary runs, so a server without ffmpeg simply sends
 * videos whole.
 */
class VideoTrimmer
{
    /**
     * A working ffmpeg is remembered for a day; a failure only for a minute,
     * so fixing the server (installing ffmpeg, allowing proc_open) shows up
     * straight away. Each failure logs why the trim bar is hidden.
     */
    public static function available(): bool
    {
        $cached = Cache::get('video-trimmer.available');
        if ($cached !== null) {
            return (bool) $cached;
        }

        $error = null;
        try {
            $result = Process::timeout(10)->run([self::binary(), '-version']);
            if (!$result->successful()) {
                $error = trim($result->errorOutput()) ?: 'exit code ' . $result->exitCode();
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        if ($error !== null) {
            Log::warning('VideoTrimmer: ffmpeg not usable, trim bar hidden', [
                'binary' => self::binary(),
                'error' => mb_substr($error, 0, 500),
            ]);
        }

        Cache::put('video-trimmer.available', $error === null, $error === null ? now()->addDay() : now()->addMinute());

        return $error === null;
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

    protected static function binary(): string
    {
        return (string) config('services.ffmpeg.binary', 'ffmpeg');
    }
}
