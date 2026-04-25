<?php

namespace App\Observers;

use App\Models\UsersProfile;
use App\Services\CacheService;
use App\Services\CacheVersion;
use App\Services\CloudflarePurge;

class ProfileObserver
{
    /**
     * Fields whose change invalidates the listing cache. If any of these
     * changes on update, bump the listing scope for this profile's city+gender.
     */
    protected array $listingFields = [
        'is_active', 'archived_at', 'package_id', 'city', 'gender',
        'name', 'slug', 'about', 'age', 'height', 'bust', 'orientation',
        'ethnicity', 'nationality', 'haircolor', 'shaved', 'smoke',
        'incall', 'outcall', 'incallprice', 'incallcurr', 'is_verified',
    ];

    public function created(UsersProfile $profile): void
    {
        CacheVersion::bump(CacheVersion::profileScope($profile->id));
        CacheVersion::bump(CacheVersion::listingScope($profile->city, $profile->gender));
        CacheVersion::bump(CacheVersion::pageScope());
        CacheService::clearHomepageCache($profile->city, $profile->gender);

        $this->purgeEdge($profile);
    }

    public function updated(UsersProfile $profile): void
    {
        CacheVersion::bump(CacheVersion::profileScope($profile->id));

        if ($profile->wasChanged($this->listingFields)) {
            CacheVersion::bump(CacheVersion::listingScope($profile->city, $profile->gender));
            CacheVersion::bump(CacheVersion::pageScope());

            // If the profile moved between city/gender, also invalidate the old scope (origin + edge).
            if ($profile->wasChanged(['city', 'gender'])) {
                $original = $profile->getOriginal();
                CacheVersion::bump(
                    CacheVersion::listingScope($original['city'] ?? null, $original['gender'] ?? null)
                );
                $this->purgeEdgeFor($original['city'] ?? null, $original['gender'] ?? null);
            }

            CacheService::clearHomepageCache($profile->city, $profile->gender);
            $this->purgeEdge($profile);
        } else {
            // Non-listing field (e.g. private note) — only the detail page needs to be edge-purged.
            $this->purgeEdgeDetail($profile);
        }
    }

    public function deleted(UsersProfile $profile): void
    {
        CacheVersion::bump(CacheVersion::profileScope($profile->id));
        CacheVersion::bump(CacheVersion::listingScope($profile->city, $profile->gender));
        CacheVersion::bump(CacheVersion::pageScope());
        CacheService::clearHomepageCache($profile->city, $profile->gender);
        $this->purgeEdge($profile);
    }

    /** Purge the listing tree for this profile's city+gender. */
    protected function purgeEdge(UsersProfile $profile): void
    {
        $this->purgeEdgeFor($profile->city, $profile->gender);
    }

    protected function purgeEdgeFor($cityId, $genderId): void
    {
        $city = CacheService::getCityById($cityId);
        $gender = CacheService::getGenderById($genderId);
        if (!$city || !$gender) {
            return;
        }

        // Prefix purge sweeps listing, pagination, query-string filters, AND all detail pages.
        CloudflarePurge::purgeListing(
            strtolower($gender->name),
            $city->slug ?: strtolower($city->name)
        );
    }

    /** Purge just the profile detail URL when the listing is unaffected. */
    protected function purgeEdgeDetail(UsersProfile $profile): void
    {
        $city = CacheService::getCityById($profile->city);
        $gender = CacheService::getGenderById($profile->gender);
        if (!$city || !$gender) {
            return;
        }

        $base = rtrim(config('services.cloudflare.site_url') ?: config('app.url'), '/');
        $genderName = strtolower($gender->name);
        $citySlug = $city->slug ?: strtolower($city->name);
        $slug = $profile->slug ?: str_replace(' ', '-', strtolower($profile->name ?? ''));

        CloudflarePurge::purgeUrls([
            "{$base}/{$genderName}-escorts-in-{$citySlug}/{$profile->id}/{$slug}",
        ]);
    }
}
