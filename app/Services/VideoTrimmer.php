<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
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
    public static function available(): bool
    {
        return Cache::remember('video-trimmer.available', now()->addHour(), function () {
            try {
                return Process::timeout(10)->run([self::binary(), '-version'])->successful();
            } catch (\Throwable $e) {
                return false;
            }
        });
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
