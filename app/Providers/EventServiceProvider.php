<?php

namespace App\Providers;

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
        Event::listen(Login::class, function () {
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
