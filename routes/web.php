<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CreateCustomer;
use App\Livewire\HomePage;
use App\Livewire\LoginController;
use App\Livewire\RegisterController;
use App\Livewire\Auth\ActivateAccount;
use App\Livewire\Profile\Users\UserProfile;
use App\Livewire\Auth\ForgetPassword;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Auth\EscortRegister;
use App\Livewire\ProfileDetails;
use App\Livewire\Profile\Users\UserDashboard;
use App\Livewire\Profile\Users\UserStatistics;
use App\Livewire\Profile\Users\RejectedVerifications;
use App\Livewire\Profile\Users\NewProfile;
use App\Http\Controllers\AjaxController;
use App\Livewire\UpgradeController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ProfileSettingController;
use App\Http\Controllers\Admin\AppSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SeoKeywordController;
use App\Http\Controllers\Admin\DefaultSeoController;
use App\Http\Controllers\Admin\UrlAliasController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Http\Controllers\PageController;
use App\Livewire\VerifyPhoto;
use App\Http\Controllers\Admin\VerificationController;
use App\Livewire\FavoriteProfilesDashboard;
use App\Livewire\Auctions\AuctionPage;
use App\Livewire\Auctions\SpotBidding;
use App\Livewire\PurchaseCredits;
use App\Livewire\NewHomepage;
use App\Livewire\MobileSearch;
use App\Livewire\NewsPage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('admin', [AuthController::class, 'checkLogin']);
Route::get('news', function(){
    return view('emails.newseletter');
});
Route::get("/", NewHomepage::class)->name("newhome");

Route::get('/search-{gender}-escorts', App\Livewire\MobileSearch::class)
    ->where('gender', 'female|male|shemale')
    ->name('mobile.search');
    Route::get('/mobile-search-results/{gender}', \App\Livewire\MobileSearchResults::class)->name('mobile.search.results');
    

Route::post('/cities/search', 'App\Http\Controllers\Api\CityController@search')->name('cities.search');

// CSRF Token Refresh Route - For automatic session recovery after idle
Route::get('/csrf-refresh', function () {
    return response()->json([
        'token' => csrf_token(),
        'success' => true
    ]);
})->name('csrf.refresh');

// WhatsApp Webhook (outside admin middleware for Meta verification)
Route::match(['get', 'post'], '/webhook/whatsapp', [\App\Http\Controllers\Admin\WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');

// News page routes - must be before the general escorts route
// All news page (without type)
Route::get("{gender}-escort-news-in-{city}", NewsPage::class)
    ->name("news.all")
    ->where([
        'gender' => 'female|male|shemale',
        'city' => '[a-z0-9\-]+'
    ]);

// Specific news type page
Route::get("{gender}-escort-news-in-{city}/{type}", NewsPage::class)
    ->name("news.page")
    ->where([
        'gender' => 'female|male|shemale',
        'city' => '[a-z0-9\-]+',
        'type' => 'new-escorts|new-reviews|new-questions'
    ]);

// Service page route - must be before the general escorts route
// Important: Parameters are passed to mount($service, $gender, $city)
Route::get("{service}-{gender}-escorts-in-{city}", App\Livewire\ServicePage::class)
    ->name("service.page")
    ->where([
        'service' => '[a-z0-9\-]+',
        'gender' => 'female|male|shemale',
        'city' => '[a-z0-9\-]+'
    ]);

// AMP Routes (for Google AMP Viewer - helps bypass blocked domains)
Route::prefix('amp')->group(function () {
    Route::get('/', [App\Http\Controllers\AmpController::class, 'home'])->name('amp.home');
    Route::get('{gender}-escorts-in-{city}', [App\Http\Controllers\AmpController::class, 'listings'])
        ->name('amp.listings')
        ->where('gender', 'female|male|shemale');
    Route::get('{gender}-escorts-in-{city}/{id}/{slug}', [App\Http\Controllers\AmpController::class, 'profile'])
        ->name('amp.profile')
        ->where('gender', 'female|male|shemale');
});

Route::get("{gender}-escorts-in-{city}/page/{page}", HomePage::class)->name("home.paginated")->where(['gender' => 'female|male|shemale', 'page' => '[0-9]+']);
Route::get("{gender}-escorts-in-{city}", HomePage::class)->name("home")->where('gender', 'female|male|shemale');
Route::get("{gender}-escorts-in-{city}/{id}/{username}", ProfileDetails::class)->where('gender', 'female|male|shemale');
Route::get("sign-in", LoginController::class)->name('sign-in');
Route::get("register", RegisterController::class)->name('register');
Route::get("escort-sign-up", EscortRegister::class)->name('escort.signup');

// Google OAuth Routes
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
 
Route::get('activate-account/{email}/{code}', ActivateAccount::class);
Route::get('forget-password', ForgetPassword::class);
Route::get('change-password/{email}/{code}', ChangePassword::class);
Route::get('/page/{slug}', \App\Livewire\PageDisplay::class);
Route::get('/about', function () {
    return view('livewire.static.about');
});
Route::get('/help-for-advertisers', function () {
    return view('livewire.static.help');
});
Route::get('/guide-to-seeing-an-escort', function () {
    return view('livewire.static.guide');
});

Route::post('searchcity', [AjaxController::class, 'citySearch']);
Route::get('api/package/{id}', [AjaxController::class, 'getPackage'])->middleware('auth');
//auth routes
Route::group(['middleware'=>'auth'], function(){
    Route::get('edit-profile/{username}/{id}', UserProfile::class)->name('user.profile');
    Route::get('action/listings/new', NewProfile::class)->name('new.profile');

    Route::post('sign_out', function(){
        \Auth::logout();
        return redirect()->route('sign-in');
    });

    Route::get("my-profile/{name}/{id}", UserDashboard::class)->name("user.dashboard");
    Route::get("my-statistics", UserStatistics::class)->name("user.statistics");
    Route::get("my-profile/{name}/{id}/upgrade/", UpgradeController::class)->name("user.upgrade");
    
    // Primary Gateway Payment Callback
    Route::get('payment/primary-callback', function() {
        return view('payment.primary-callback');
    })->name('payment.primary.callback');
    
    Route::get("select-package", \App\Livewire\Profile\Users\NewProfileUpgrade::class)->name("upgrade.profile.new");
    Route::get('my-profile/{slug}/{id}/verify-photo', [App\Http\Controllers\VerifyPhotoController::class, 'index'])->name('verify.photo');
    Route::get('rejected-verifications', RejectedVerifications::class)->name('rejected.verifications');
    Route::get('archived-profiles', \App\Livewire\Profile\Users\ArchivedProfiles::class)->name('archived.profiles');
    Route::get("my-account", \App\Livewire\UserAccount::class)->name("user.account");
    Route::get("my-account/edit", \App\Livewire\Profile\UserAccountEdit::class)->name("user.account.edit");
    Route::get("my-password/edit", \App\Livewire\Profile\UserAccountPassword::class)->name("user.account.password");
    Route::get("my-account/newsletter/edit", \App\Livewire\NewsletterSettings::class)->name("user.account.newsletter");
    Route::post("my-account/delete", [App\Http\Controllers\UserAccountController::class, 'deleteAccount'])->name("user.account.delete");
    Route::get("my-messages", \App\Livewire\Messages::class)->name("user.messages");
    Route::get("my-chat", \App\Livewire\Chat::class)->name("user.chat");
    Route::get("my-chat/{userId}", \App\Livewire\Chat::class)->name("user.chat.with");
    Route::get("my-questions", \App\Livewire\Questions::class)->name("user.questions");
    Route::get("my-reviews", \App\Livewire\Reviews::class)->name("user.reviews");
    Route::post('my-profile/{slug}/{id}/verify-photo', [App\Http\Controllers\VerifyPhotoController::class, 'store'])->name('verify.photo.store');
    Route::get('/api/search-my-profiles', [App\Http\Controllers\VerifyPhotoController::class, 'searchProfiles'])->name('api.search.profiles');
    Route::get('/my-favorites', FavoriteProfilesDashboard::class)->name('favorites.dashboard');
    Route::get('questions', App\Livewire\QuestionManagement::class)->name('users.questions');
    
    // Profile status toggle
    Route::post('profile-status/{id}', function(Illuminate\Http\Request $request, $id) {
        $profile = App\Models\UsersProfile::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $action = $request->input('action');
        $profile->is_active = $action;
        $profile->save();
        
        return response()->json([
            'success' => true,
            'message' => $action == 1 ? 'Profile resumed successfully!' : 'Profile paused successfully!'
        ]);
    })->name('profile.status.toggle');
    
    // Clear upgrade modal session
    Route::post('clear-upgrade-modal', function() {
        session()->forget('show_upgrade_modal');
        return response()->json(['success' => true]);
    })->name('clear.upgrade.modal');
    
    // Profile reactivation routes
    Route::get('profile-status', [App\Http\Controllers\ProfileReactivationController::class, 'dashboard'])->name('profile.status');
    Route::get('archived-profiles', [App\Http\Controllers\ProfileReactivationController::class, 'index'])->name('profile.archived');
    Route::post('profile/{id}/reactivate', [App\Http\Controllers\ProfileReactivationController::class, 'reactivate'])->name('profile.reactivate');
    
    // Exit impersonation route (accessible from frontend)
    Route::get('exit-impersonation', [App\Http\Controllers\Admin\AdminUserController::class, 'exitImpersonation'])->name('exit.impersonation');

    Route::get('auctions/{gender?}-escorts-in-{city?}', AuctionPage::class)->name('auctions.index');
    Route::get('auctions/{gender?}-escorts-in-{cityname?}/spot/{spot}', SpotBidding::class);
    Route::get('purchase-credits', PurchaseCredits::class)->name('purchase.credits');


});


//admin routes
Route::group(['prefix'=>'admin', 'namespace' => 'App\Http\Controllers\Admin'], function(){
Route::get('/', [AuthController::class, 'checkLogin']);
    Route::get('login', [AuthController::class, 'index'])->name('admin.login');
    Route::post('login', [AuthController::class, 'loginPost'])->name('admin.loginpost');

    Route::group(['middleware'=>['web', 'admin']], function(){
        Route::get('dashboard', [HomeController::class, 'index'])->name('admin.dashboard');

        //cities
        Route::get('cities', [CityController::class, 'index'])->name('admin.cities');
        Route::post('addcity', [CityController::class, 'addCity'])->name('admin.addcity');
        Route::post('updatecity', [CityController::class, 'updateCity'])->name('admin.updatecity');
        Route::post('delcity', [CityController::class, 'delCity'])->name('admin.delcity');
        Route::post('toggle-city-sitemap', [CityController::class, 'toggleCitySitemap'])->name('admin.toggleCitySitemap');
        Route::post('toggle-city-featured', [CityController::class, 'toggleCityFeatured'])->name('admin.toggleCityFeatured');
        Route::post('update-feature-priority', [CityController::class, 'updateFeaturePriority'])->name('admin.updateFeaturePriority');

        //country
        Route::get('countries', [CountryController::class, 'index'])->name('admin.countries');
        Route::post('addcountry', [CountryController::class, 'addCountry'])->name('admin.addcountry');
        Route::post('updatecountry', [CountryController::class, 'updateCountry'])->name('admin.updatecountry');
        Route::post('delcountry', [CountryController::class, 'delCountry'])->name('admin.delcountry');

        //currency
        Route::get('currencies', [CurrencyController::class, 'index'])->name('admin.currencies');
        Route::post('addcurrency', [CurrencyController::class, 'addCurrency'])->name('admin.addcurrency');
        Route::post('updatecurrency', [CurrencyController::class, 'updateCurrency'])->name('admin.updatecurrency');
        Route::post('delcurrency', [CurrencyController::class, 'delCurrency'])->name('admin.delcurrency');

        //gender
        Route::get('genders', [ProfileSettingController::class, 'genders'])->name('admin.genders');
        Route::post('addgender', [ProfileSettingController::class, 'addGender'])->name('admin.addgender');
        Route::post('updategender', [ProfileSettingController::class, 'updateGender'])->name('admin.updategender');
        Route::post('delgender', [ProfileSettingController::class, 'delGender'])->name('admin.delgender');

        //Haircolor
        Route::get('haircolors', [ProfileSettingController::class, 'hairColors'])->name('admin.haircolors');
        Route::post('addhaircolor', [ProfileSettingController::class, 'addHairColor'])->name('admin.addhaircolor');
        Route::post('updatehaircolor', [ProfileSettingController::class, 'updateHairColor'])->name('admin.updatehaircolor');
        Route::post('delhaircolor', [ProfileSettingController::class, 'delHairColor'])->name('admin.delhaircolor');

         //Busts
        Route::get('busts', [ProfileSettingController::class, 'busts'])->name('admin.busts');
        Route::post('addbust', [ProfileSettingController::class, 'addBust'])->name('admin.addbust');
        Route::post('updatebust', [ProfileSettingController::class, 'updateBust'])->name('admin.updatebust');
        Route::post('delbust', [ProfileSettingController::class, 'delBust'])->name('admin.delbust');

        //orientation
        Route::get('orientations', [ProfileSettingController::class, 'orientations'])->name('admin.orientations');
        Route::post('addorientation', [ProfileSettingController::class, 'addOrientation'])->name('admin.addorientation');
        Route::post('updateorientation', [ProfileSettingController::class, 'updateOrientation'])->name('admin.updateorientation');
        Route::post('delorientation', [ProfileSettingController::class, 'delOrientation'])->name('admin.delorientation');


         //ethincity
        Route::get('ethnicities', [ProfileSettingController::class, 'ethnicities'])->name('admin.ethnicities');
        Route::post('addethnicity', [ProfileSettingController::class, 'addEthnicity'])->name('admin.addethnicity');
        Route::post('updateethnicity', [ProfileSettingController::class, 'updateEthnicity'])->name('admin.updateethnicity');
        Route::post('delethnicity', [ProfileSettingController::class, 'delEthnicity'])->name('admin.delethnicity');

         //Languages
        Route::get('languages', [ProfileSettingController::class, 'languages'])->name('admin.languages');
        Route::post('addlanguage', [ProfileSettingController::class, 'addLanguage'])->name('admin.addlanguage');
        Route::post('updatelanguage', [ProfileSettingController::class, 'updateLanguage'])->name('admin.updatelanguage');
        Route::post('dellanguage', [ProfileSettingController::class, 'delLanguage'])->name('admin.dellanguage');
 
         Route::get('app-setting', [AppSettingController::class, 'index'])->name('admin.appsetting');
         Route::post('app-setting-update', [AppSettingController::class, 'update'])->name('admin.appsettings.update');
         Route::post('clear-cache', [AppSettingController::class, 'clearCache'])->name('admin.clear-cache');

         Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

         // User CRUD
Route::get('users', [AdminUserController::class, 'index'])->name('admin.users');
Route::get('getuser/{id}', [AdminUserController::class, 'getUser'])->name('admin.getuser');
Route::post('adduser', [AdminUserController::class, 'storeUser'])->name('admin.adduser');
Route::post('updateuser/{id}', [AdminUserController::class, 'updateUser'])->name('admin.updateuser');
Route::post('deleteuser', [AdminUserController::class, 'deleteUser'])->name('admin.deleteuser');
Route::post('toggleuserstatus', [AdminUserController::class, 'toggleUserStatus'])->name('admin.toggleuserstatus');
Route::post('sendverification', [AdminUserController::class, 'sendVerificationEmail'])->name('admin.sendverification');
Route::post('impersonate', [AdminUserController::class, 'impersonate'])->name('admin.impersonate');// UsersProfile CRUD
Route::post('getprofiles', [AdminUserController::class, 'getProfiles'])->name('admin.getprofiles');
Route::post('addprofile', [AdminUserController::class, 'storeProfile'])->name('admin.addprofile');
Route::post('updateprofile/{id}', [AdminUserController::class, 'updateProfile'])->name('admin.updateprofile');
Route::post('deleteprofile', [AdminUserController::class, 'deleteProfile'])->name('admin.deleteprofile');
Route::post('assign-package', [AdminUserController::class, 'assignPackage'])->name('admin.assignpackage');


Route::get('getprofile', [AdminUserController::class, 'getProfile'])->name('admin.getprofile');
Route::post('updateprofile', [AdminUserController::class, 'updateProfile'])->name('admin.updateprofile.post');
Route::post('deleteprofile', [AdminUserController::class, 'deleteProfile'])->name('admin.deleteprofile.post');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::patch('/reviews/{id}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::patch('/reviews/{id}/disapprove', [ReviewController::class, 'disapprove'])->name('reviews.disapprove');
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');


Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::patch('/questions/{id}/approve', [QuestionController::class, 'approve'])->name('questions.approve');
Route::patch('/questions/{id}/disapprove', [QuestionController::class, 'disapprove'])->name('questions.disapprove');
Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->name('questions.destroy');


Route::name('seo.')->group(function () {
    Route::get('/seo', [SeoKeywordController::class, 'index'])->name('index');
    Route::get('/seo/create', [SeoKeywordController::class, 'create'])->name('create');
    Route::post('/seo', [SeoKeywordController::class, 'store'])->name('store');
    Route::get('/seo/{seoKeyword}/edit', [SeoKeywordController::class, 'edit'])->name('edit');
    Route::put('/seo/{seoKeyword}', [SeoKeywordController::class, 'update'])->name('update');
    Route::delete('/seo/{seoKeyword}', [SeoKeywordController::class, 'destroy'])->name('destroy');
});

// Default SEO Settings Management
Route::name('default-seo.')->group(function () {
    Route::get('/default-seo', [DefaultSeoController::class, 'index'])->name('index');
    Route::get('/default-seo/create', [DefaultSeoController::class, 'create'])->name('create');
    Route::post('/default-seo', [DefaultSeoController::class, 'store'])->name('store');
    Route::get('/default-seo/{defaultSeoSetting}/edit', [DefaultSeoController::class, 'edit'])->name('edit');
    Route::put('/default-seo/{defaultSeoSetting}', [DefaultSeoController::class, 'update'])->name('update');
    Route::delete('/default-seo/{defaultSeoSetting}', [DefaultSeoController::class, 'destroy'])->name('destroy');
});

// URL Aliases Management
Route::name('admin.url-aliases.')->group(function () {
    Route::get('/url-aliases', [UrlAliasController::class, 'index'])->name('index');
    Route::get('/url-aliases/create', [UrlAliasController::class, 'create'])->name('create');
    Route::post('/url-aliases', [UrlAliasController::class, 'store'])->name('store');
    Route::get('/url-aliases/{urlAlias}/edit', [UrlAliasController::class, 'edit'])->name('edit');
    Route::put('/url-aliases/{urlAlias}', [UrlAliasController::class, 'update'])->name('update');
    Route::delete('/url-aliases/{urlAlias}', [UrlAliasController::class, 'destroy'])->name('destroy');
    Route::patch('/url-aliases/{urlAlias}/toggle', [UrlAliasController::class, 'toggle'])->name('toggle');
});

// API route for getting SEO content (outside admin middleware)
Route::get('/api/seo-content', [SeoKeywordController::class, 'getSeoTags'])->name('api.seo.content');


Route::get('packages', 'PackageController@index')->name('admin.packages');
Route::get('global-packages', [PackageController::class, 'globalPackages'])->name('admin.global.packages');
Route::post('/package', [PackageController::class, 'store'])->name('admin.addpackage');
Route::get('/package/{id}', [PackageController::class, 'getPackage'])->name('admin.getpackage');
Route::get('/packages/{id}', [PackageController::class, 'show'])->name('admin.packages.show');
Route::put('/packages/{id}', [PackageController::class, 'update'])->name('admin.packages.update');
Route::delete('/packages/{id}', [PackageController::class, 'destroy'])->name('admin.packages.destroy');
Route::get('/packages/by-country/{countryId}', [PackageController::class, 'getPackagesByCountry'])->name('admin.packages.by.country');
Route::post('/package/update', [PackageController::class, 'update'])->name('admin.updatepackage');
Route::post('/package/delete', [PackageController::class, 'delete'])->name('admin.delpackage');
Route::get('packages/all', [PackageController::class, 'getAllPackages'])->name('admin.getallpackages');

Route::get('/wallet', [PackageController::class, 'wallet'])->name('admin.wallet');
Route::post('/wallet/topup', [PackageController::class, 'topup'])->name('admin.wallet.topup');
Route::post('/wallet/transfer', [PackageController::class, 'transfer'])->name('admin.wallet.transfer');
Route::post('/wallet/validate-user', [PackageController::class, 'validateUser'])->name('admin.wallet.validate-user');
Route::get('/wallet/transactions', [PackageController::class, 'transactions'])->name('admin.wallet.transactions');
Route::get('wallet/transactions/{user_id}', [PackageController::class, 'getUserTransactions'])->name('admin.wallet.user.transactions');
   
Route::get('profile-settings', [ProfileSettingController::class, 'getsettingview'])->name('admin.profile.settings');
    Route::post('profile-settings', [ProfileSettingController::class, 'accupdate'])->name('admin.profile.update');
    Route::resource('pages', AdminPageController::class)->except(['show']);
    Route::post('pages/update-order', [AdminPageController::class, 'updateOrder'])->name('admin.pages.update-order');
    
    // Profile Image Management (must be before /profiles/{id} routes to avoid conflicts)
    Route::post('/profiles/{id}/images/upload', [ProfileSettingController::class, 'uploadImages'])->name('admin.profiles.images.upload');
    Route::delete('/profile-images/{imageId}', [ProfileSettingController::class, 'deleteImage'])->name('admin.profiles.images.delete');
    Route::post('/profiles/{id}/images/reorder', [ProfileSettingController::class, 'reorderImages'])->name('admin.profiles.images.reorder');
    
    Route::get('/profiles', [ProfileSettingController::class, 'index'])->name('admin.profiles.index');
    Route::get('/profiles/{id}/edit', [ProfileSettingController::class, 'edit'])->name('admin.profiles.edit');
    Route::put('/profiles/{id}', [ProfileSettingController::class, 'update'])->name('admin.profiles.update');
    Route::delete('/profiles/bulk-delete', [ProfileSettingController::class, 'bulkDestroy'])->name('admin.profiles.bulk-delete');
    Route::delete('/profiles/{id}', [ProfileSettingController::class, 'destroy'])->name('admin.profiles.destroy');
   Route::get('/package/{id}', [ProfileSettingController::class, 'getPackage'])->name('admin.package.get');
    
    // Archive routes
    Route::post('/profiles/{id}/archive', [ProfileSettingController::class, 'archive'])->name('admin.profiles.archive');
    Route::post('/profiles/{id}/repost', [ProfileSettingController::class, 'repost'])->name('admin.profiles.repost');
    Route::post('/profiles/bulk-archive', [ProfileSettingController::class, 'bulkArchive'])->name('admin.profiles.bulk-archive');
    Route::post('/profiles/bulk-repost', [ProfileSettingController::class, 'bulkRepost'])->name('admin.profiles.bulk-repost');

    // Profile Reports Management
    Route::get('/profile-reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.profile-reports.index');
    Route::get('/profile-reports/{id}', [App\Http\Controllers\Admin\ReportController::class, 'show'])->name('admin.profile-reports.show');
    Route::post('/profile-reports/{id}/status', [App\Http\Controllers\Admin\ReportController::class, 'updateStatus'])->name('admin.profile-reports.update-status');
    Route::post('/profile-reports/{id}/action', [App\Http\Controllers\Admin\ReportController::class, 'takeAction'])->name('admin.profile-reports.action');
    Route::delete('/profile-reports/{id}', [App\Http\Controllers\Admin\ReportController::class, 'destroy'])->name('admin.profile-reports.destroy');

    Route::get('/listings', [PackageController::class, 'listings'])->name('admin.listings.index');
    Route::get('/listings/create', [PackageController::class, 'createListing'])->name('admin.listings.create');
    Route::post('/listings', [PackageController::class, 'storeListing'])->name('admin.listings.store');
    Route::get('/listings/{listing}/edit', [PackageController::class, 'editListing'])->name('admin.listings.edit');
    Route::put('/listings/{listing}', [PackageController::class, 'updateListing'])->name('admin.listings.update');
    Route::delete('/listings/{listing}', [PackageController::class, 'destroyListing'])->name('admin.listings.destroy');


    Route::get('/verifications', [VerificationController::class, 'index'])->name('admin.verifications');
    Route::post('/verifications/{id}/approve', [VerificationController::class, 'approve']);
    Route::post('/verifications/{id}/reject', [VerificationController::class, 'reject']);

    // URL Redirects
    Route::get('/url-redirects', [App\Http\Controllers\Admin\UrlRedirectController::class, 'index'])->name('admin.url-redirects.index');
    Route::post('/url-redirects', [App\Http\Controllers\Admin\UrlRedirectController::class, 'store'])->name('admin.url-redirects.store');
    Route::put('/url-redirects/{id}', [App\Http\Controllers\Admin\UrlRedirectController::class, 'update'])->name('admin.url-redirects.update');
    Route::delete('/url-redirects/{id}', [App\Http\Controllers\Admin\UrlRedirectController::class, 'destroy'])->name('admin.url-redirects.destroy');
    Route::patch('/url-redirects/{id}/toggle-status', [App\Http\Controllers\Admin\UrlRedirectController::class, 'toggleStatus'])->name('admin.url-redirects.toggle-status');


    Route::get('/auctions', [\App\Http\Controllers\Admin\AuctionController::class, 'index'])
    ->name('admin.auctions.index');
Route::get('/auctions/create', [\App\Http\Controllers\Admin\AuctionController::class, 'create'])
    ->name('admin.auctions.create');
Route::post('/auctions', [\App\Http\Controllers\Admin\AuctionController::class, 'store'])
    ->name('admin.auctions.store');
Route::get('/auctions/{auction}/edit', [\App\Http\Controllers\Admin\AuctionController::class, 'edit'])
    ->name('admin.auctions.edit');
Route::put('/auctions/{auction}', [\App\Http\Controllers\Admin\AuctionController::class, 'update'])
    ->name('admin.auctions.update');
Route::delete('/auctions/{auction}', [\App\Http\Controllers\Admin\AuctionController::class, 'destroy'])
    ->name('admin.auctions.destroy');

// Additional auction management routes
Route::post('/auctions/{auction}/end', [\App\Http\Controllers\Admin\AuctionController::class, 'endAuction'])
    ->name('admin.auctions.end');
Route::post('/auctions/{auction}/reset', [\App\Http\Controllers\Admin\AuctionController::class, 'resetAuction'])
    ->name('admin.auctions.reset');
Route::get('/auctions/{auction}/bids', [\App\Http\Controllers\Admin\AuctionController::class, 'bids'])
    ->name('admin.auctions.bids');
Route::post('/auctions/{auction}/bids/{bid}/award', [\App\Http\Controllers\Admin\AuctionController::class, 'awardSpot'])
    ->name('admin.auctions.award');

    Route::post('/cities/search', function(Illuminate\Http\Request $request) {
        $search = $request->input('search');
        $cities = App\Models\City::where('name', 'like', "%{$search}%")
            ->orWhere('country', 'like', "%{$search}%")
            ->limit(20)
            ->get(['id', 'name', 'country'])
            ->map(function($city) {
                // Count profiles in this city
                $profileCount = \App\Models\UsersProfile::where('city', $city->id)
                    ->whereNull('archived_at')
                    ->where('is_active', 1)
                    ->count();
                
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'country' => $city->country,
                    'profile_count' => $profileCount
                ];
            });
            
        return response()->json($cities);
    })->name('admin.cities.search');

    Route::post('auctions/update-city', 'AuctionController@updateCity')->name('admin.auctions.updateCity');

    // Reports Routes
    Route::name('admin.reports.')->group(function () {
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
        Route::get('/reports/dashboard', [\App\Http\Controllers\Admin\ReportsController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports/wallet', [\App\Http\Controllers\Admin\ReportsController::class, 'walletReports'])->name('wallet');
        Route::get('/reports/balance-transfers', [\App\Http\Controllers\Admin\ReportsController::class, 'balanceTransfers'])->name('balance-transfers');
        Route::get('/reports/paid-ads', [\App\Http\Controllers\Admin\ReportsController::class, 'paidAds'])->name('paid-ads');
        Route::get('/reports/export/{type}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportCsv'])->name('export');
    });

    // Mail Settings Routes
    Route::name('admin.mail-settings.')->group(function () {
        Route::get('/mail-settings', [\App\Http\Controllers\Admin\MailSettingsController::class, 'index'])->name('index');
        Route::post('/mail-settings', [\App\Http\Controllers\Admin\MailSettingsController::class, 'store'])->name('store');
        Route::post('/mail-settings/test', [\App\Http\Controllers\Admin\MailSettingsController::class, 'testConnection'])->name('test');
    });

    // Demo Email Route (for testing purposes)
    Route::get('/send-demo-email/{email?}', function($email = null) {
        $email = $email ?: 'test@example.com';
        
        $sampleUser = (object) [
            'name' => 'Demo User',
            'email' => $email,
            'created_at' => now(),
        ];
        
        try {
            \Mail::to($email)->send(new \App\Mail\WelcomeEmail($sampleUser));
            return response()->json(['success' => true, 'message' => 'Demo email sent to ' . $email]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    })->name('admin.demo.email');

    // Test Account Creation Email Toggle
    Route::get('/test-account-creation-toggle/{email?}', function($email = null) {
        $email = $email ?: 'test@example.com';
        
        // Simulate what happens during registration
        $shouldSend = \App\Models\MailSettings::shouldSendEmail('account_create');
        
        $response = [
            'account_creation_emails_enabled' => $shouldSend,
            'email_would_be_sent_to' => $email,
            'message' => $shouldSend 
                ? 'Account creation email WOULD be sent (enabled in settings)' 
                : 'Account creation email WOULD NOT be sent (disabled in settings)',
        ];
        
        if ($shouldSend) {
            try {
                // Simulate sending the NewUser email
                $sampleData = [
                    'name' => 'Test User',
                    'random' => \Illuminate\Support\Str::random(40),
                    'email' => $email,
                ];
                
                \Mail::to($email)->send(new \App\Mail\NewUser($sampleData));
                $response['actual_email_sent'] = true;
                $response['message'] .= ' - Test email actually sent!';
            } catch (\Exception $e) {
                $response['actual_email_sent'] = false;
                $response['error'] = $e->getMessage();
            }
        }
        
        return response()->json($response, 200, [], JSON_PRETTY_PRINT);
    })->name('admin.test.account.creation');

    // WhatsApp Messaging Routes
    Route::name('admin.whatsapp.')->prefix('whatsapp')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('index');
        Route::get('/quick-chat', [\App\Http\Controllers\Admin\WhatsAppController::class, 'quickChat'])->name('quick-chat');
        Route::post('/generate-link', [\App\Http\Controllers\Admin\WhatsAppController::class, 'generateLink'])->name('generate-link');
        Route::post('/clear-history', [\App\Http\Controllers\Admin\WhatsAppController::class, 'clearHistory'])->name('clear-history');
        Route::get('/messages/{conversationId}', [\App\Http\Controllers\Admin\WhatsAppController::class, 'getMessages'])->name('messages');
        Route::post('/send', [\App\Http\Controllers\Admin\WhatsAppController::class, 'sendMessage'])->name('send');
        Route::post('/quick-send', [\App\Http\Controllers\Admin\WhatsAppController::class, 'sendQuickMessage'])->name('quick-send');
        Route::post('/start-conversation', [\App\Http\Controllers\Admin\WhatsAppController::class, 'startConversation'])->name('start-conversation');
        Route::delete('/conversation/{id}', [\App\Http\Controllers\Admin\WhatsAppController::class, 'deleteConversation'])->name('delete-conversation');
        Route::get('/settings', [\App\Http\Controllers\Admin\WhatsAppController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\WhatsAppController::class, 'updateSettings'])->name('update-settings');
        
        // WhatsApp Rotation Messages Routes
        Route::name('rotation.')->prefix('rotation')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'toggleStatus'])->name('toggle');
            Route::post('/reorder', [\App\Http\Controllers\Admin\WhatsAppRotationController::class, 'reorder'])->name('reorder');
        });
    });
 
    // Telegram Messaging Routes
    Route::name('admin.telegram.')->prefix('telegram')->group(function () {
        Route::get('/quick-chat', [\App\Http\Controllers\Admin\TelegramController::class, 'quickChat'])->name('quick-chat');
        Route::post('/generate-link', [\App\Http\Controllers\Admin\TelegramController::class, 'generateLink'])->name('generate-link');
        Route::post('/clear-history', [\App\Http\Controllers\Admin\TelegramController::class, 'clearHistory'])->name('clear-history');
    });

    // Blog Management Routes
    Route::name('admin.blog.')->prefix('blog')->group(function () {
        // Blog Posts
        Route::get('/posts', [\App\Http\Controllers\Admin\BlogPostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [\App\Http\Controllers\Admin\BlogPostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [\App\Http\Controllers\Admin\BlogPostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'show'])->name('posts.show');
        Route::get('/posts/{post}/edit', [\App\Http\Controllers\Admin\BlogPostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\BlogPostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/{post}/remove-image', [\App\Http\Controllers\Admin\BlogPostController::class, 'removeImage'])->name('posts.remove-image');
        Route::post('/posts/{post}/update-status', [\App\Http\Controllers\Admin\BlogPostController::class, 'updateStatus'])->name('posts.update-status');
        
        // Blog Categories
        Route::get('/categories', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\BlogCategoryController::class, 'destroy'])->name('categories.destroy');
        
        // Blog Tags
        Route::get('/tags', [\App\Http\Controllers\Admin\BlogTagController::class, 'index'])->name('tags.index');
        Route::get('/tags/create', [\App\Http\Controllers\Admin\BlogTagController::class, 'create'])->name('tags.create');
        Route::post('/tags', [\App\Http\Controllers\Admin\BlogTagController::class, 'store'])->name('tags.store');
        Route::get('/tags/{tag}/edit', [\App\Http\Controllers\Admin\BlogTagController::class, 'edit'])->name('tags.edit');
        Route::put('/tags/{tag}', [\App\Http\Controllers\Admin\BlogTagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{tag}', [\App\Http\Controllers\Admin\BlogTagController::class, 'destroy'])->name('tags.destroy');
        
        // Blog Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\BlogSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\BlogSettingController::class, 'update'])->name('settings.update');
    });

    });
});

// Test route for SEO service
Route::get('/test-seo/{url?}', function($url = null) {
    $testUrl = $url ?: request()->getPathInfo();
    
    if ($url) {
        $testUrl = '/' . $url;
    }
    
    $seoService = app(\App\Services\SeoService::class);
    $seoData = $seoService->getSeoFromUrl($testUrl);
    
    // Get additional debug info
    $gender = \App\Models\Gender::where('name', 'like', '%female%')->first();
    $city = \App\Models\City::where('name', 'like', '%dubai%')->first();
    $seoRecord = null;
    
    if ($gender && $city) {
        $seoRecord = \App\Models\SeoKeyword::where('gender_id', $gender->id)
                                          ->where('city_id', $city->id)
                                          ->first();
    }
    
    return response()->json([
        'url' => $testUrl,
        'seo_data' => $seoData,
        'debug' => [
            'gender_found' => $gender ? $gender->toArray() : null,
            'city_found' => $city ? $city->toArray() : null,
            'seo_record_found' => $seoRecord ? $seoRecord->toArray() : null,
        ]
    ]);
})->where('url', '.*');

// Test route for Default SEO fallback
Route::get('/test-default-seo/{context?}', function($context = 'global') {
    $seoService = app(\App\Services\SeoService::class);
    
    // Test scenarios including static pages
    $scenarios = [
        'homepage' => '/',
        'login_page' => '/login',
        'register_page' => '/register',
        'contact_page' => '/contact',
        'about_page' => '/about',
        'dashboard_page' => '/dashboard',
        'non-existent-page' => '/non-existent-page',
        'female-dubai' => '/female-escorts-in-dubai',
        'male-abu-dhabi' => '/male-escorts-in-abu-dhabi',
        'random-combo' => '/transgender-escorts-in-sharjah'
    ];
    
    $results = [];
    
    foreach ($scenarios as $name => $url) {
        $seoData = $seoService->getSeoFromUrl($url);
        $results[$name] = [
            'url' => $url,
            'title' => $seoData['title'],
            'description' => substr($seoData['description'], 0, 100) . '...',
            'keywords' => substr($seoData['keywords'], 0, 50) . '...',
            'source' => $seoData['seo_record'] ? 'specific_seo' : 
                       (isset($seoData['default_seo_setting']) && $seoData['default_seo_setting'] ? 'default_seo' : 'site_settings'),
            'default_seo_name' => $seoData['default_seo_setting']->name ?? null,
        ];
    }
    
    // Also show available default SEO settings
    $defaultSettings = \App\Models\DefaultSeoSetting::active()->byPriority()->get();
    
    return response()->json([
        'message' => 'SEO Test Results - Each URL shows which SEO source is being used',
        'scenarios' => $results,
        'available_default_settings' => $defaultSettings->map(function($setting) {
            return [
                'name' => $setting->name,
                'title' => $setting->title,
                'priority' => $setting->priority,
                'is_active' => $setting->is_active
            ];
        }),
        'legend' => [
            'specific_seo' => 'Found exact SEO match in seo_keywords table',
            'default_seo' => 'Using default SEO setting (fallback)',
            'site_settings' => 'Using site settings (last resort)'
        ]
    ]);
});

// Demo route to show Default SEO in action on frontend
Route::get('/demo-seo-pages', function() {
    return view('demo-seo-test');
});

// Test login page with default SEO
Route::get('/test-login', function() {
    return view('test-login');
});

// Test register page with default SEO  
Route::get('/test-register', function() {
    return view('test-register');
});

// Test view for SEO (admin only)
Route::get('/admin/test-seo-view', function() {
    return view('seo-test');
});

// URL alias handler - fallback route for custom URLs
Route::fallback(function(\Illuminate\Http\Request $request) {
    $path = trim($request->getPathInfo(), '/');
    
    // Check if this is a custom alias
    $alias = \App\Models\UrlAlias::where('custom_url', $path)
                                ->where('is_active', true)
                                ->first();
    
    if ($alias) {
        switch ($alias->redirect_type) {
            case '301':
                return redirect('/' . $alias->base_pattern, 301);
                
            case '302':
                return redirect('/' . $alias->base_pattern, 302);
                
            case 'canonical':
                // Parse the base pattern to extract gender and city
                if (preg_match('/^([a-zA-Z_]+)-escorts-in-([a-zA-Z-]+)$/', $alias->base_pattern, $matches)) {
                    $gender = $matches[1];
                    $city = $matches[2];
                    
                    // Set canonical URL for SEO while keeping the original URL
                    view()->share([
                        'canonicalUrl' => url($alias->base_pattern),
                        'isAliasUrl' => true,
                        'currentUrl' => url($path),
                        'aliasRecord' => $alias
                    ]);
                    
                    // Create a sub-request for the base pattern
                    $subRequest = \Illuminate\Http\Request::create(
                        '/' . $alias->base_pattern,
                        'GET',
                        $request->query(),
                        $request->cookies->all(),
                        $request->files->all(),
                        $request->server->all()
                    );
                    
                    // Copy headers
                    foreach ($request->headers->all() as $name => $values) {
                        $subRequest->headers->set($name, $values);
                    }
                    
                    // Handle the sub-request through the application
                    return app()->handle($subRequest);
                }
                break;
        }
    }
    
    // No alias found, return 404
    abort(404);
});

// Sitemap routes
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('sitemaps/pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('sitemaps/cities.xml', [SitemapController::class, 'cities'])->name('sitemap.cities');
Route::get('sitemaps/profiles.xml', [SitemapController::class, 'profiles'])->name('sitemap.profiles');
Route::get('sitemaps/categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');

// Test route for country detection
Route::get('/test-country-detection', function() {
    $domain = request()->getHost();
    $prefix = getDomainPrefix();
    $country = getCurrentCountry();
    
    // Get country-specific packages
    $countryPackages = \App\Models\Package::with(['countryPrices' => function($query) use ($country) {
        if ($country) {
            $query->where('country_id', $country->id);
        }
    }])
    ->where('is_global', false)
    ->whereHas('countryPrices', function($query) use ($country) {
        if ($country) {
            $query->where('country_id', $country->id);
        }
    })
    ->get();
    
    // Get global packages
    $globalPackages = \App\Models\Package::where('is_global', true)->get();
    
    // Merge packages
    $allPackages = $countryPackages->merge($globalPackages);
    
    return response()->json([
        'domain' => $domain,
        'prefix' => $prefix,
        'detected_country' => [
            'id' => $country->id ?? null,
            'name' => $country->nicename ?? 'Not Found',
            'domain_prefix' => $country->domain_prefix ?? null,
        ],
        'packages_for_country' => $allPackages->map(function($pkg) use ($country) {
            $isGlobal = $pkg->is_global;
            return [
                'id' => $pkg->id,
                'name' => $pkg->name,
                'is_global' => $isGlobal,
                'has_pricing_for_country' => $isGlobal || $pkg->countryPrices->count() > 0,
                'price_tiers' => $isGlobal ? $pkg->price_tiers : ($pkg->countryPrices->first()->price_tiers ?? null)
            ];
        })
    ]);
});
