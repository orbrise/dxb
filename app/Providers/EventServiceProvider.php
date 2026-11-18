<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UsersProfile;
use App\Services\SupportWelcome;
use App\Support\IpLocation;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // evoory_auth marks an authenticated browser so Cloudflare can bypass the
        // edge HTML cache for logged-in users. The page.cache middleware already
        // bypasses origin for them, but CF caches the guest render of "/" and
        // serves it back to everyone — including logged-in users — unless a
        // CF Cache Rule keys on this cookie. Value is meaningless; presence is
        // the signal. Lifetime tracks session.lifetime so the cookie disappears
        // when the session would have expired anyway.
        Event::listen(Login::class, function (Login $event) {
            // New users get the Support welcome message on their first login.
            // Not when an admin impersonates them: that isn't the user.
            if ($event->user instanceof User && !session('admin_impersonating')) {
                SupportWelcome::sendOnFirstLogin($event->user);

                // Where they logged in from (every login path fires this:
                // email, Google, activation link, profile claim).
                $userId = $event->user->id;
                IpLocation::captureAfterResponse($userId, 'login', null, function ($ip, $country) use ($userId) {
                    User::whereKey($userId)->update([
                        'last_login_ip'      => $ip,
                        'last_login_country' => $country,
                        'last_login_at'      => now(),
                    ]);
                });
            }

            Cookie::queue(
                'evoory_auth',
                '1',
                (int) config('session.lifetime', 120),
                '/',
                null,
                (bool) config('session.secure', false),
                false,
                false,
                'lax'
            );
        });

        // Registration and profile creation already store the IP + country on
        // the row (every sign-up path and both profile-create paths); copy
        // them to the location history. Rows without an IP (scraper imports,
        // admin-created users) aren't someone's location.
        User::created(function (User $user) {
            if ($user->registration_ip) {
                IpLocation::log($user->id, 'register', null, $user->registration_ip, $user->registration_country, request()->userAgent());
            }
        });
        UsersProfile::created(function (UsersProfile $profile) {
            if ($profile->ip_address && $profile->user_id) {
                IpLocation::log((int) $profile->user_id, 'profile_create', $profile->id, $profile->ip_address, $profile->ip_country, request()->userAgent());
            }
        });

        Event::listen(Logout::class, function () {
            Cookie::queue(Cookie::forget('evoory_auth', '/'));
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
