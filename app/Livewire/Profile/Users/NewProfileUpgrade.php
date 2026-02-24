<?php

namespace App\Livewire\Profile\Users;

use Livewire\Component;
use App\Models\Package;
use App\Models\ProfileImage;
use App\Models\UserLanguage;
use App\Models\UserService;
use App\Models\UsersProfile;
use DB;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Models\MailSettings;
use App\Mail\ProfileCreated;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;

class NewProfileUpgrade extends Component
{
    public $selectedPaymentMethod;
    public $selectedPackage;
    public $selectedDuration;
    public $selectedAmount;
    public $paypalOrderId;
    public $paypalClientId = 'AXEOnZr8asYD0Wav8y-eNaDpv_Vj80sF1wvMXV-iu4V6MMnZWolAo1xfwXJvakd-QSsi7qBHnjZcvFlr';

    protected $listeners = ['processWalletPayment', 'handlePayPalApproval', 'processPrimaryPayment'];

    public function mount()
    {
        // Check if session data exists
        $sessionData = session('new_profile_data_' . auth()->id());
        
        \Log::info('NewProfileUpgrade mount', [
            'user_id' => auth()->id(),
            'session_key' => 'new_profile_data_' . auth()->id(),
            'has_session_data' => !empty($sessionData),
            'session_id' => session()->getId()
        ]);
        
        if (!$sessionData) {
            session()->flash('error', 'Session data expired. Please fill the form again.');
            return $this->redirect(route('new.profile'), navigate: true);
        }
    }

    public function render()
    {
        // Get session data first
        $sessionData = session('new_profile_data_' . auth()->id());
        
        // Get country from session data's city
        $currentCountry = null;
        if ($sessionData && isset($sessionData['city'])) {
            $city = \App\Models\City::find($sessionData['city']);
            if ($city && $city->country) {
                // City has country name, find the country by name
                $currentCountry = \App\Models\Country::where('nicename', $city->country)->first();
            }
        }
        
        // Fallback to domain-based detection if no city country found
        if (!$currentCountry) {
            $currentCountry = function_exists('getCurrentCountry') 
                ? getCurrentCountry() 
                : \App\Models\Country::where('nicename', 'United Arab Emirates')->first();
        }
        
        // Load country-specific packages
        $countryPackages = Package::with(['countryPrices' => function($query) use ($currentCountry) {
            $query->where('country_id', $currentCountry->id);
        }])
        ->where('is_global', false)
        ->whereHas('countryPrices', function($query) use ($currentCountry) {
            $query->where('country_id', $currentCountry->id);
        })
        ->orderBy('id', 'asc')
        ->get();
        
        // If country has specific packages, use them. Otherwise, use global packages
        if ($countryPackages->count() > 0) {
            $packages = $countryPackages;
        } else {
            // Load global packages only when no country-specific packages exist
            $packages = Package::where('is_global', true)
                ->orderBy('id', 'asc')
                ->get();
        }
        
        // Prepare profile data for preview
        $profileName = $sessionData['name'] ?? 'Profile Name';
        $profileAbout = $sessionData['aboutme'] ?? 'Profile description will appear here...';
        $profileImage = null;
        
        // Get first image from temp paths if available
        if (isset($sessionData['tempImagePaths']) && !empty($sessionData['tempImagePaths'])) {
            $mainImageIndex = $sessionData['mainImage'] ?? 0;
            if (isset($sessionData['tempImagePaths'][$mainImageIndex])) {
                $profileImage = $sessionData['tempImagePaths'][$mainImageIndex];
            } else {
                $profileImage = $sessionData['tempImagePaths'][0];
            }
        }
        
        return view('livewire.profile.users.new-profile-upgrade', compact('packages', 'profileName', 'profileAbout', 'profileImage'));
    }

    public function processWalletPayment($packageId = null, $duration = null, $amount = null)
    {
        // Use passed parameters or fall back to class properties
        $selectedPackage = $packageId ?? $this->selectedPackage;
        $selectedDuration = $duration ?? $this->selectedDuration;
        $selectedAmount = $amount ?? $this->selectedAmount;
        
        $user = auth()->user();
        $userEmail = $user->email ?? 'unknown';
        $userId = $user->id;
        $wallet = $user->wallet;
        
        \Log::info('[NEW PROFILE] Wallet Payment Started', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'package_id' => $selectedPackage,
            'duration' => $selectedDuration,
            'amount' => $selectedAmount,
            'payment_method' => 'wallet'
        ]);

        // Create wallet if it doesn't exist
        if (!$wallet) {
            $wallet = $user->wallet()->create(['balance' => 0]);
            \Log::info('[NEW PROFILE] Created new wallet for user', [
                'user_id' => $userId,
                'user_email' => $userEmail
            ]);
        }

        if ($wallet->balance < $selectedAmount) {
            \Log::warning('[NEW PROFILE] Wallet Payment Failed - Insufficient balance', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'wallet_balance' => $wallet->balance,
                'required_amount' => $selectedAmount
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Insufficient balance in wallet'
            ]);
            return;
        }

        // Check session data first
        $sessionData = session('new_profile_data_' . auth()->id());
        if (!$sessionData) {
            \Log::error('[NEW PROFILE] Wallet Payment Failed - Session data expired', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'session_key' => 'new_profile_data_' . auth()->id()
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Session data expired. Please try again.'
            ]);
            return $this->redirect(route('new.profile'), navigate: true);
        }
        
        \Log::info('[NEW PROFILE] Session data found, proceeding with profile creation', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'has_name' => isset($sessionData['name']),
            'has_images' => isset($sessionData['tempImagePaths']) ? count($sessionData['tempImagePaths']) : 0
        ]);

        $createdProfile = null;
        
        try {
            DB::transaction(function() use ($wallet, &$createdProfile, $selectedPackage, $selectedDuration, $selectedAmount, $userEmail, $userId) {
                \Log::info('[NEW PROFILE] Wallet Transaction Starting - Creating profile', [
                    'user_id' => $userId,
                    'user_email' => $userEmail
                ]);
                
                // Create profile with package
                $profile = $this->createProfileFromSession($selectedPackage, $selectedDuration);
                
                if (!$profile) {
                    \Log::error('[NEW PROFILE] Failed to create profile from session', [
                        'user_id' => $userId,
                        'user_email' => $userEmail
                    ]);
                    throw new \Exception('Failed to create profile');
                }
                
                $createdProfile = $profile;
                
                \Log::info('[NEW PROFILE] Profile created successfully', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->name,
                    'profile_slug' => $profile->slug
                ]);

                // Create wallet transaction record
                WalletTransaction::create([
                    'user_id' => auth()->id(),
                    'wallet_id' => $wallet->id,
                    'amount' => $selectedAmount,
                    'type' => 'package_purchase',
                    'status' => 'completed',
                    'description' => 'Package purchase payment for new profile',
                    'package_id' => $selectedPackage,
                ]);

                // Deduct from wallet
                $wallet->decrement('balance', $selectedAmount);
                
                \Log::info('[NEW PROFILE] Wallet Transaction Completed', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'profile_id' => $profile->id,
                    'amount_deducted' => $selectedAmount
                ]);
                
                // Send profile created email if enabled in settings
                if (MailSettings::shouldSendEmail('account_create')) {
                    try {
                    $package = Package::find($selectedPackage);
                    $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                    
                    Mail::to(auth()->user()->email)->send(new ProfileCreated([
                        'profileName' => $profile->name,
                        'packageName' => $package ? $package->name : null,
                        'profileUrl' => $profileUrl,
                    ]));
                    
                    \Log::info('Profile created email sent', ['profile_id' => $profile->id, 'user_id' => auth()->id()]);
                } catch (\Exception $e) {
                    \Log::error('[NEW PROFILE] Profile created email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                }
            }
        });
        
            \Log::info('[NEW PROFILE] Wallet Payment Success - Redirecting', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $createdProfile ? $createdProfile->id : null,
                'profile_name' => $createdProfile ? $createdProfile->name : null
            ]);

            // Redirect after transaction completes
            if ($createdProfile) {
                $redirectUrl = "/my-profile/{$createdProfile->slug}/{$createdProfile->id}";
                $this->js("setTimeout(function() { window.location.href = '{$redirectUrl}'; }, 500);");
            }
        } catch (\Exception $e) {
            \Log::error('[NEW PROFILE] Wallet Payment Exception', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Profile creation failed. Please try again.'
            ]);
        }
    }

    public function processPrimaryPayment($packageId = null, $duration = null, $amount = null, $referenceId = null)
    {
        $user = auth()->user();
        $userEmail = $user->email ?? 'unknown';
        $userId = $user->id;
        
        \Log::info('[NEW PROFILE] Primary Gateway Payment Started', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'package_id' => $packageId,
            'duration' => $duration,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'payment_method' => 'primary_gateway'
        ]);
        
        // Prevent duplicate transactions - check if this reference ID was already processed
        if ($referenceId) {
            $existingTransaction = WalletTransaction::where('description', 'LIKE', '%' . $referenceId . '%')
                ->where('user_id', auth()->id())
                ->where('type', 'primary_gateway_payment')
                ->first();
                 
            if ($existingTransaction) {
                \Log::warning('[NEW PROFILE] Duplicate Primary Gateway payment attempt blocked', [
                    'reference_id' => $referenceId,
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'existing_transaction_id' => $existingTransaction->id
                ]);
                
                // Check if profile was already created and redirect
                $profile = UsersProfile::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($profile) {
                    $redirectUrl = "/my-profile/{$profile->slug}/{$profile->id}";
                    $this->js("setTimeout(function() { window.location.href = '{$redirectUrl}'; }, 500);");
                }
                return;
            }
        }
        
        // Use passed parameters or fall back to class properties
        $selectedPackage = $packageId ?? $this->selectedPackage;
        $selectedDuration = $duration ?? $this->selectedDuration;
        $selectedAmount = $amount ?? $this->selectedAmount;

        // Check session data first
        $sessionData = session('new_profile_data_' . auth()->id());
        if (!$sessionData) {
            \Log::error('[NEW PROFILE] Primary Gateway Payment Failed - Session data expired', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'reference_id' => $referenceId,
                'session_key' => 'new_profile_data_' . auth()->id()
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Session data expired. Please try again.'
            ]);
            return $this->redirect(route('new.profile'), navigate: true);
        }
        
        \Log::info('[NEW PROFILE] Primary Gateway - Session data found, proceeding', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'reference_id' => $referenceId,
            'has_name' => isset($sessionData['name']),
            'has_images' => isset($sessionData['tempImagePaths']) ? count($sessionData['tempImagePaths']) : 0
        ]);

        $createdProfile = null;
        
        try {
            DB::transaction(function() use ($user, &$createdProfile, $selectedPackage, $selectedDuration, $selectedAmount, $referenceId, $userEmail, $userId) {
                \Log::info('[NEW PROFILE] Primary Gateway Transaction Starting - Creating profile', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'reference_id' => $referenceId
                ]);
                
                // Create profile with package
                $profile = $this->createProfileFromSession($selectedPackage, $selectedDuration);
                
                if (!$profile) {
                    \Log::error('[NEW PROFILE] Primary Gateway - Failed to create profile from session', [
                        'user_id' => $userId,
                        'user_email' => $userEmail,
                        'reference_id' => $referenceId
                    ]);
                    throw new \Exception('Failed to create profile');
                }
            
                $createdProfile = $profile;
                
                \Log::info('[NEW PROFILE] Primary Gateway - Profile created successfully', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->name,
                    'profile_slug' => $profile->slug,
                    'reference_id' => $referenceId
                ]);

                // Ensure user has a wallet for transaction tracking
                $wallet = $user->wallet;
                if (!$wallet) {
                    $wallet = $user->wallet()->create(['balance' => 0]);
                }

                // Create wallet transaction record for tracking
                WalletTransaction::create([
                    'user_id' => auth()->id(),
                    'wallet_id' => $wallet->id,
                    'amount' => $selectedAmount,
                    'type' => 'primary_gateway_payment',
                    'status' => 'completed',
                    'description' => 'Primary Gateway package purchase for new profile - Ref: ' . $referenceId,
                    'package_id' => $selectedPackage,
                ]);
                
                \Log::info('[NEW PROFILE] Primary Gateway Transaction Record Created', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'profile_id' => $profile->id,
                    'reference_id' => $referenceId,
                    'amount' => $selectedAmount
                ]);
                
                // Send profile created email if enabled in settings
                if (MailSettings::shouldSendEmail('account_create')) {
                    try {
                        $package = Package::find($selectedPackage);
                        $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                        
                        Mail::to(auth()->user()->email)->send(new ProfileCreated([
                            'profileName' => $profile->name,
                            'packageName' => $package ? $package->name : null,
                            'profileUrl' => $profileUrl,
                        ]));
                        
                        \Log::info('[NEW PROFILE] Profile created email sent (Primary Gateway)', ['profile_id' => $profile->id, 'user_email' => $userEmail]);
                    } catch (\Exception $e) {
                        \Log::error('[NEW PROFILE] Profile created email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                    }
                }
            });
            
            \Log::info('[NEW PROFILE] Primary Gateway Payment Success - Redirecting', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $createdProfile ? $createdProfile->id : null,
                'profile_name' => $createdProfile ? $createdProfile->name : null,
                'reference_id' => $referenceId
            ]);

            // Redirect after transaction completes
            if ($createdProfile) {
                $redirectUrl = "/my-profile/{$createdProfile->slug}/{$createdProfile->id}";
                $this->js("setTimeout(function() { window.location.href = '{$redirectUrl}'; }, 500);");
            }
        } catch (\Exception $e) {
            \Log::error('[NEW PROFILE] Primary Gateway Payment Exception', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'reference_id' => $referenceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Profile creation failed. Please contact support.'
            ]);
        }
    }

    public function createPayPalOrder()
    {
        // Validate required data
        if (!$this->selectedPackage || !$this->selectedDuration || !$this->selectedAmount) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Please select a package and duration'
            ]);
            return;
        }

        // Get package details
        $package = Package::find($this->selectedPackage);
        if (!$package) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Package not found'
            ]);
            return;
        }

        // Create PayPal order data
        $orderData = [
            'package_id' => $this->selectedPackage,
            'duration' => $this->selectedDuration,
            'amount' => $this->selectedAmount,
            'is_new_profile' => true
        ];

        // Store order data in session for later verification
        session(['paypal_order_data_new_profile' => $orderData]);

        // Return order data to the frontend
        $this->dispatch('paypalOrderCreated', [
            'amount' => $this->selectedAmount,
            'currency' => 'EUR',
            'description' => $package->name . ' for ' . $this->selectedDuration . ' days'
        ]);
    }

    public function handlePayPalApproval($paypalOrderId)
    {
        $user = auth()->user();
        $userEmail = $user->email ?? 'unknown';
        $userId = $user->id;
        
        \Log::info('[NEW PROFILE] PayPal Payment Started', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'paypal_order_id' => $paypalOrderId,
            'payment_method' => 'paypal'
        ]);
        
        // Get the stored order data
        $orderData = session('paypal_order_data_new_profile');
        
        if (!$orderData) {
            \Log::error('[NEW PROFILE] PayPal Payment Failed - Order data not found in session', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'paypal_order_id' => $paypalOrderId
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Order data not found'
            ]);
            return;
        }
        
        \Log::info('[NEW PROFILE] PayPal Payment - Order data found', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'order_data' => $orderData,
            'paypal_order_id' => $paypalOrderId
        ]);

        try {
            // Create profile with package
            $profile = $this->createProfileFromSession($orderData['package_id'], $orderData['duration']);
            
            if (!$profile) {
                \Log::error('[NEW PROFILE] PayPal Payment Failed - Could not create profile from session', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'paypal_order_id' => $paypalOrderId
                ]);
                $this->dispatch('showMessage', [
                    'type' => 'error',
                    'message' => 'Session data expired. Please try again.'
                ]);
                return $this->redirect(route('new.profile'), navigate: true);
            }
            
            \Log::info('[NEW PROFILE] PayPal Payment - Profile created successfully', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id,
                'profile_name' => $profile->name,
                'paypal_order_id' => $paypalOrderId
            ]);

            // Send profile upgrade email if enabled in admin settings
            if (MailSettings::shouldSendEmail('profile_upgrade')) {
                try {
                    $package = Package::find($orderData['package_id']);
                    NotificationService::sendProfileUpgradeEmail($user, $package);
                    \Log::info('[NEW PROFILE] PayPal - Profile upgrade email sent', ['profile_id' => $profile->id, 'user_email' => $userEmail]);
                } catch (\Exception $e) {
                    \Log::error('[NEW PROFILE] PayPal - Profile upgrade email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                }
            }

            // Create transaction record
            $wallet = $user->wallet;
            if (!$wallet) {
                $wallet = $user->wallet()->create(['balance' => 0]);
            }

            WalletTransaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $wallet->id,
                'amount' => $orderData['amount'],
                'type' => 'paypal_payment',
                'status' => 'completed',
                'description' => 'PayPal payment for new profile package - Order: ' . $paypalOrderId,
                'reference' => $paypalOrderId,
                'package_id' => $orderData['package_id'],
            ]);
            
            \Log::info('[NEW PROFILE] PayPal Transaction Record Created', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'paypal_order_id' => $paypalOrderId,
                'amount' => $orderData['amount']
            ]);

            // Clear session data
            session()->forget('paypal_order_data_new_profile');

            // Send email if enabled in settings
            if (MailSettings::shouldSendEmail('account_create')) {
                try {
                    $package = Package::find($orderData['package_id']);
                    $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                    
                    Mail::to(auth()->user()->email)->send(new ProfileCreated([
                        'profileName' => $profile->name,
                        'packageName' => $package ? $package->name : null,
                        'profileUrl' => $profileUrl,
                    ]));
                    
                    \Log::info('[NEW PROFILE] PayPal - Profile created email sent', ['profile_id' => $profile->id, 'user_email' => $userEmail]);
                } catch (\Exception $e) {
                    \Log::error('[NEW PROFILE] PayPal - Profile created email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                }
            }
            
            \Log::info('[NEW PROFILE] PayPal Payment Success - Redirecting', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id,
                'profile_name' => $profile->name,
                'paypal_order_id' => $paypalOrderId
            ]);

            // Use JavaScript redirect for reliability
            $redirectUrl = "/my-profile/{$profile->slug}/{$profile->id}";
            $this->js("setTimeout(function() { window.location.href = '{$redirectUrl}'; }, 500);");
        } catch (\Exception $e) {
            \Log::error('[NEW PROFILE] PayPal Payment Exception', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Profile creation failed. Please contact support.'
            ]);
        }
    }

    public function skipPackage()
    {
        // Check if session data exists first
        $sessionData = session('new_profile_data_' . auth()->id());
        if (!$sessionData) {
            session()->flash('error', 'Session data expired. Please fill the form again.');
            return $this->redirect(route('new.profile'), navigate: true);
        }
        
        // Create profile without package (free profile)
        $profile = $this->createProfileFromSession(null, 0);
        
        if (!$profile) {
            return $this->redirect(route('new.profile'), navigate: true);
        }

        // Send email if enabled in settings
        if (MailSettings::shouldSendEmail('account_create')) {
            try {
                $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                
                Mail::to(auth()->user()->email)->send(new ProfileCreated([
                    'profileName' => $profile->name,
                    'packageName' => null,
                    'profileUrl' => $profileUrl,
                ]));
                
                \Log::info('Profile created email sent', ['profile_id' => $profile->id, 'user_id' => auth()->id()]);
            } catch (\Exception $e) {
                \Log::error('Profile created email failed: ' . $e->getMessage());
            }
        }

        // Use JavaScript redirect for reliability when called from event listener
        $redirectUrl = "/my-profile/{$profile->slug}/{$profile->id}";
        $this->js("setTimeout(function() { window.location.href = '{$redirectUrl}'; }, 500);");
    }

    private function createProfileFromSession($packageId, $duration)
    {
        // Retrieve session data
        $sessionData = session('new_profile_data_' . auth()->id());
        
        if (!$sessionData) {
            return null;
        }

        // Create profile with package_id
        $profile = UsersProfile::create([
            'user_id' => auth()->id(),
            'name' => $sessionData['name'],
            'slug' => Str::slug($sessionData['name']),
            'listing' => $sessionData['listing'],
            'city' => $sessionData['city'],
            'about' => $sessionData['aboutme'],
            'phone' => $sessionData['phone'],
            'country_code' => $sessionData['countrycode'],
            'iswhatsapp' => $sessionData['iswhatsapp'],
            'istelegram' => $sessionData['istelegram'],
            'iswechat' => $sessionData['iswechat'],
            'issignal' => $sessionData['issignal'],
            'phone2' => $sessionData['phone2'],
            'country_code2' => $sessionData['countrycode2'] ?? null,
            'iswhatsapp2' => $sessionData['iswhatsapp2'],
            'istelegram2' => $sessionData['istelegram2'],
            'iswechat2' => $sessionData['iswechat2'],
            'issignal2' => $sessionData['issignal2'],
            'website' => $sessionData['website'],
            'onlyfans' => $sessionData['onlyfans'],
            'gender' => $sessionData['gender'],
            'incall' => $sessionData['incall'] ? true : false,
            'outcall' => $sessionData['outcall'] ? true : false,
            'incallprice' => $sessionData['incallprice'],
            'incallcurr' => $sessionData['incallcurr'],
            'outcallprice' => $sessionData['outcallprice'],
            'outcallcurr' => $sessionData['outcallcurr'],
            'orientation' => $sessionData['ori'],
            'ethnicity' => $sessionData['ethnicity'],
            'height' => $sessionData['height'],
            'nationality' => $sessionData['nationality'],
            'age' => $sessionData['age'],
            'haircolor' => $sessionData['haircolor'],
            'bust' => $sessionData['bust'],
            'shaved' => $sessionData['shaved'],
            'smoke' => $sessionData['smoke'],
            'video' => $sessionData['video'],
            'ip_address' => $sessionData['ip_address'] ?? null,
            'ip_country' => $sessionData['ip_country'] ?? null,
            'package_id' => $packageId,
            'package_days' => $packageId ? $duration : null,
            'is_featured' => $packageId ? true : false,
            'promoted_until' => $packageId ? now()->addDays($duration) : null,
        ]);
        
        \Log::info('Profile Created in NewProfileUpgrade', [
            'profile_id' => $profile->id,
            'ip_address' => $profile->ip_address,
            'ip_country' => $profile->ip_country,
        ]);

        // Process images from stored paths
        $externalDisk = Storage::disk('assets_external');
        $basePath = "userimages/{$profile->user_id}/{$profile->id}";

        // Create directory structure on external disk
        if (!$externalDisk->exists("userimages")) {
            $externalDisk->makeDirectory("userimages", 0755, true);
        }

        if (!$externalDisk->exists("userimages/{$profile->user_id}")) {
            $externalDisk->makeDirectory("userimages/{$profile->user_id}", 0755, true);
        }

        if (!$externalDisk->exists($basePath)) {
            $externalDisk->makeDirectory($basePath, 0755, true);
        }
       
        // Process and save images from temporary paths
        foreach($sessionData['tempImagePaths'] as $key => $imagePath) {
            if (!file_exists($imagePath)) {
                continue; // Skip if temporary file no longer exists
            }

            $fileName = time() . rand(100,999);
            
            $manager = new ImageManager(new Driver());
            $img = $manager->read($imagePath);
            
            // Optimize and save images
            if ($img->width() > $img->height()) {
                $img->scaleDown(width: 1920);
            } else {
                $img->scaleDown(height: 1920);
            }
            
            // Create temporary files
            $tempJpgPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
            $tempWebpPath = sys_get_temp_dir() . '/' . uniqid() . '.webp';
            
            // Save to temporary files first
            $img->save($tempJpgPath, quality: 75);
            $img->encode(new WebpEncoder(quality: 75))->save($tempWebpPath);
            
            // Upload to external disk
            $externalDisk->put("{$basePath}/{$fileName}.jpg", file_get_contents($tempJpgPath));
            $externalDisk->put("{$basePath}/{$fileName}.webp", file_get_contents($tempWebpPath));
            
            // Clean up temporary files
            if (file_exists($tempJpgPath)) unlink($tempJpgPath);
            if (file_exists($tempWebpPath)) unlink($tempWebpPath);
            
            ProfileImage::create([
                'user_id' => $profile->user_id,
                'profile_id' => $profile->id,
                'image' => $fileName.'.jpg',
                'image_webp' => $fileName.'.webp',
                'is_main' => $key == $sessionData['mainImage'] || ($sessionData['mainImage'] == 0 && $key == 0)
            ]);
        }
        
        // Attach languages
        $languages = [
            ['lang' => $sessionData['language1'] ?? null, 'level' => $sessionData['expert1'] ?? null],
            ['lang' => $sessionData['language2'] ?? null, 'level' => $sessionData['expert2'] ?? null],
            ['lang' => $sessionData['language3'] ?? null, 'level' => $sessionData['expert3'] ?? null],
            ['lang' => $sessionData['language4'] ?? null, 'level' => $sessionData['expert4'] ?? null],
            ['lang' => $sessionData['language5'] ?? null, 'level' => $sessionData['expert5'] ?? null],
        ];

        collect($languages)
            ->filter(fn($lang) => $lang['lang'])
            ->each(fn($lang) => UserLanguage::create([
                'user_id' => auth()->id(),
                'language_id' => $lang['lang'],
                'expert' => $lang['level'],
                'profile_id' => $profile->id
            ]));
        
        // Attach services
        if (isset($sessionData['services']) && is_array($sessionData['services'])) {
            collect($sessionData['services'])->each(fn($service) => 
                UserService::create([
                    'user_id' => auth()->id(),
                    'service_id' => $service,
                    'profile_id' => $profile->id
                ])
            );
        }

        // Clear session data
        session()->forget('new_profile_data_' . auth()->id());

        return $profile;
    }
}
