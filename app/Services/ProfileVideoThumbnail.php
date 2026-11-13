<?php

namespace App\Services;

use App\Models\ProfileVideo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Makes sure a profile video has a real poster image. The browser captures
 * one at upload time, but for a long time that capture drew the clip before
 * any frame was decoded and saved a solid-black JPEG, so listings showed
 * black video tiles. A missing or black thumbnail is replaced with a frame
 * rendered by ffmpeg (VideoTrimmer's binary); without ffmpeg it's left alone.
 */
class ProfileVideoThumbnail
{
    // Same cap as the browser capture (_profile-video-thumbnailer).
    protected const MAX_SIDE = 720;

    /**
     * Replaces $video's thumbnail when it's missing or black (or always, with
     * $force). The new file gets a new name: the CDN caches the old URL.
     * Returns whether the thumbnail changed.
     */
    public static function ensure(ProfileVideo $video, bool $force = false): bool
    {
        $disk = Storage::disk('assets_external');
        $dir = "uservideos/{$video->user_id}/{$video->profile_id}";

        $current = $video->video_thumbnail ? "{$dir}/{$video->video_thumbnail}" : null;
        if (!$force && !self::needed($video)) {
            return false;
        }
        if (!$video->video || !$disk->exists("{$dir}/{$video->video}") || !VideoTrimmer::available()) {
            return false;
        }

        $jpeg = self::render($disk->path("{$dir}/{$video->video}"));
        // A dark clip renders dark too; keep the file it has rather than
        // writing another copy on every run.
        if ($jpeg === null || ($current && $disk->exists($current) && self::isBlank($jpeg))) return false;

        $name = pathinfo($video->video, PATHINFO_FILENAME) . '-' . Str::lower(Str::random(6)) . '.jpg';
        $disk->put("{$dir}/{$name}", $jpeg);
        $video->forceFill(['video_thumbnail' => $name])->saveQuietly();
        if ($current && $current !== "{$dir}/{$name}") $disk->delete($current);

        return true;
    }

    /** Whether $video's thumbnail is missing (no name, or no file) or black. */
    public static function needed(ProfileVideo $video): bool
    {
        if (!$video->video_thumbnail) return true;
        $disk = Storage::disk('assets_external');
        $path = "uservideos/{$video->user_id}/{$video->profile_id}/{$video->video_thumbnail}";

        return !$disk->exists($path) || self::isBlank((string) $disk->get($path));
    }

    /**
     * True when a JPEG is (nearly) all black — what a frame grabbed before the
     * video decoded looks like. A dark but real frame still has some brighter
     * pixels. Without GD we can't tell, so it counts as fine.
     */
    public static function isBlank(string $jpeg): bool
    {
        if ($jpeg === '' || !function_exists('imagecreatefromstring')) return $jpeg === '';
        $image = @imagecreatefromstring($jpeg);
        if (!$image) return true;

        $width = imagesx($image);
        $height = imagesy($image);
        $brightest = 0;
        for ($x = 0; $x < $width; $x += max(1, intdiv($width, 24))) {
            for ($y = 0; $y < $height; $y += max(1, intdiv($height, 24))) {
                $rgb = imagecolorat($image, $x, $y);
                $brightest = max($brightest, (($rgb >> 16) & 255) + (($rgb >> 8) & 255) + ($rgb & 255));
            }
        }
        imagedestroy($image);

        return $brightest < 60; // out of 765 (white)
    }

    /**
     * A representative frame as JPEG bytes, or null. Looks from 1 s in first
     * (clips often open on black) and lets ffmpeg's thumbnail filter pick the
     * most typical of the next frames; very short clips fall back to 0 s.
     */
    protected static function render(string $source): ?string
    {
        $base = tempnam(sys_get_temp_dir(), 'pvt');
        @unlink($base);
        $target = $base . '.jpg';
        $fallback = null;
        try {
            foreach (['1', '0'] as $seek) {
                @unlink($target);
                $result = Process::timeout(60)->run([
                    VideoTrimmer::binary(), '-hide_banner', '-loglevel', 'error', '-y',
                    '-ss', $seek, '-i', $source,
                    '-vf', 'thumbnail,scale=w=\'min(' . self::MAX_SIDE . ',iw)\':h=\'min(' . self::MAX_SIDE . ',ih)\':force_original_aspect_ratio=decrease',
                    '-frames:v', '1', '-q:v', '4',
                    $target,
                ]);
                if (!$result->successful() || !is_file($target) || filesize($target) === 0) continue;

                $jpeg = (string) file_get_contents($target);
                if (!self::isBlank($jpeg)) return $jpeg;
                $fallback ??= $jpeg;
            }
            // Every try was black: the clip really is dark; still better than nothing.
            return $fallback;
        } catch (\Throwable $e) {
            Log::warning('ProfileVideoThumbnail: ffmpeg failed', ['source' => $source, 'error' => $e->getMessage()]);
            return null;
        } finally {
            @unlink($target);
        }
    }
}
