<?php

namespace App\Support;

use App\Models\Package;
use App\Models\UsersProfile;

/**
 * "Contact Support" payment option on the profile upgrade pages (new profile
 * + existing profile). Builds the Support chat link with a ready-to-send
 * note naming the profile (title + id), the package the user picked and
 * the account email, so
 * support knows exactly what to upgrade once payment is sorted out.
 * Same &draft= mechanism as Buy Credits → Contact Support (Chat::mount).
 */
class SupportUpgradeChat
{
    public static function draft(UsersProfile $profile, $packageId = null, $duration = null, $amount = null): string
    {
        $package = $packageId ? Package::find($packageId) : null;

        $plan = '';
        if ($package) {
            $plan = ' to ' . $package->name;
            if ((int) $duration > 0) {
                $plan .= ' for ' . (int) $duration . ' days';
            }
            if (is_numeric($amount) && $amount > 0) {
                $amount = (float) $amount;
                $plan .= ' ($' . ($amount == floor($amount) ? (int) $amount : number_format($amount, 2, '.', '')) . ')';
            }
        }

        // The account email, so support can find the user without asking.
        $email = $profile->user?->email ?? auth()->user()?->email;
        $contact = $email ? " My email: {$email}." : '';

        return "Hi, I'd like to upgrade my profile \"{$profile->name}\" (ID {$profile->id}){$plan}.{$contact} How can I pay?";
    }

    public static function url(UsersProfile $profile, $packageId = null, $duration = null, $amount = null): string
    {
        return route('user.chat', [
            'support' => 1,
            'draft' => self::draft($profile, $packageId, $duration, $amount),
        ]);
    }
}
