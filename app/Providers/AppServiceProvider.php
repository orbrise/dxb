<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        View::share('setting', Setting::find(1));
        
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
