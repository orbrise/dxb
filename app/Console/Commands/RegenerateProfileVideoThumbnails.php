<?php

namespace App\Console\Commands;

use App\Models\ProfileVideo;
use App\Services\ProfileVideoThumbnail;
use App\Services\VideoTrimmer;
use Illuminate\Console\Command;

/**
 * Backfill for the black video tiles on listings: uploads used to save a
 * solid-black thumbnail (see ProfileVideoThumbnail). Renders a real frame
 * with ffmpeg for every profile video whose thumbnail is missing or black.
 */
class RegenerateProfileVideoThumbnails extends Command
{
    protected $signature = 'profile-videos:thumbnails
        {--dry-run : List the videos that need a new thumbnail without changing anything}
        {--force : Re-render every thumbnail, not only missing or black ones}
        {--profile= : Only this listing (profile ID)}';

    protected $description = 'Replace missing or black profile video thumbnails with a frame rendered by ffmpeg';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if (!$dryRun && !VideoTrimmer::available()) {
            $this->error('ffmpeg is not usable: ' . VideoTrimmer::status()['reason']);
            $this->line('Set FFMPEG_BINARY in .env to the ffmpeg binary (e.g. ~/bin/ffmpeg), then run this again.');
            return self::FAILURE;
        }

        $query = ProfileVideo::query()->orderBy('id');
        if ($this->option('profile')) $query->where('profile_id', (int) $this->option('profile'));

        $checked = $needed = $fixed = $failed = 0;
        $query->chunkById(100, function ($videos) use ($dryRun, $force, &$checked, &$needed, &$fixed, &$failed) {
            foreach ($videos as $video) {
                $checked++;
                if (!$force && !ProfileVideoThumbnail::needed($video)) continue;
                $needed++;

                if ($dryRun) {
                    $this->line("Needs a thumbnail: video #{$video->id} (listing #{$video->profile_id})");
                    continue;
                }
                if (ProfileVideoThumbnail::ensure($video, $force)) {
                    $fixed++;
                    $this->line("<info>✓</info> video #{$video->id} (listing #{$video->profile_id}) → {$video->video_thumbnail}");
                } else {
                    $failed++;
                    $this->line("<comment>–</comment> video #{$video->id} (listing #{$video->profile_id}): no usable frame (file missing, or the clip is all black)");
                }
            }
        });

        $this->newLine();
        $this->info($dryRun
            ? "Checked {$checked} videos: {$needed} need a new thumbnail."
            : "Checked {$checked} videos: {$fixed} thumbnails replaced, {$failed} could not be.");

        return self::SUCCESS;
    }
}
