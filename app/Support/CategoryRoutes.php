<?php

namespace App\Support;

use App\Models\Listing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CategoryRoutes
{
    /**
     * Return a `slug1|slug2|slug3` regex pattern of every non-escort listing
     * slug so the /{category}-in-{city} route can whitelist matches without
     * colliding with unrelated URLs. Cached because route registration runs
     * on every request; returns '' when the listings table or slug column is
     * not present yet (e.g. before migrations run on a fresh clone).
     */
    public static function nonEscortSlugsPattern(): string
    {
        return Cache::remember('routes:non_escort_category_slugs', 3600, function () {
            try {
                if (!Schema::hasTable('listings') || !Schema::hasColumn('listings', 'slug')) {
                    return '';
                }
            } catch (\Throwable $e) {
                return '';
            }

            $slugs = Listing::query()
                ->where('is_escort_category', false)
                ->whereNotNull('slug')
                ->orderBy('sort_order')
                ->pluck('slug')
                ->filter(fn ($s) => is_string($s) && $s !== '' && preg_match('/^[a-z0-9\-]+$/', $s))
                ->values()
                ->all();

            return implode('|', $slugs);
        });
    }
}
