<?php

namespace App\Observers;

use App\Models\ProfileImage;
use App\Services\CacheService;
use App\Services\CacheVersion;
use App\Services\CloudflarePurge;

class ProfileImageObserver
{
    public function created(ProfileImage $image): void
    {
        $this->invalidate($image);
    }

    public function updated(ProfileImage $image): void
    {
        $this->invalidate($image);
    }

    public function deleted(ProfileImage $image): void
    {
        $this->invalidate($image);
    }

    protected function invalidate(ProfileImage $image): void
    {
        // A profile's images appear on both the profile detail and the listing thumbnails.
        CacheVersion::bump(CacheVersion::profileScope($image->profile_id));

        $profile = $image->relationLoaded('profile') ? $image->profile : \App\Models\UsersProfile::find($image->profile_id);
        if ($profile) {
            CacheVersion::bump(CacheVersion::listingScope($profile->city, $profile->gender));

            $city = CacheService::getCityById($profile->city);
            $gender = CacheService::getGenderById($profile->gender);
            if ($city && $gender) {
                CloudflarePurge::purgeListing(
                    strtolower($gender->name),
                    $city->slug ?: strtolower($city->name)
                );
            }
        }
    }
}
