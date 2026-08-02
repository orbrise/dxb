<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use View;
use App\Models\Setting;
use App\Models\UsersProfile;
use App\Models\Review;
use App\Models\ProfileImage;
use App\Models\Question;
use App\Models\Message;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\View\Composers\SeoComposer;
use App\Observers\ProfileObserver;
use App\Observers\ReviewObserver;
use App\Observers\ProfileImageObserver;
use App\Observers\QuestionObserver;
use App\Observers\AuctionObserver;
use App\Observers\AuctionBidObserver;
use App\Observers\MessageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cache Setting::find(1) for 24h. Used in nearly every layout/view via
        // $setting->favicon, $setting->app_logo, $setting->app_name, etc. Without
        // the cache this fired on every web request AND every artisan command,
        // adding a DB roundtrip and crashing the whole app when MySQL was down.
        // Settings change rarely; admins can run `php artisan cache:forget app:setting`
        // (or clear the cache) to refresh sooner.
        //
        // Guard against caching null: if Setting::find(1) ever returns null (transient
        // DB blip, race during migrate, etc.) the null would stick for 24h and break
        // every admin page's `$setting->favicon` access. Re-query live and refuse to
        // persist a null value.
        $setting = Cache::remember('app:setting', 86400, fn() => Setting::find(1));
        if (!$setting) {
            Cache::forget('app:setting');
            $setting = Setting::find(1);
            if ($setting) {
                Cache::put('app:setting', $setting, 86400);
            }
        }
        View::share('setting', $setting);
        
        // Register SEO View Composer for all views
        View::composer('*', SeoComposer::class);
        
        // Register model observers for cache invalidation
        UsersProfile::observe(ProfileObserver::class);
        Review::observe(ReviewObserver::class);
        ProfileImage::observe(ProfileImageObserver::class);
        Question::observe(QuestionObserver::class);
        Auction::observe(AuctionObserver::class);
        AuctionBid::observe(AuctionBidObserver::class);
        
        // Register message observer for real-time broadcasting
        Message::observe(MessageObserver::class);
    }
}
