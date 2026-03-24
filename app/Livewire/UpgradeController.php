<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Package;
use App\Models\UsersProfile;
use DB;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Models\MailSettings;
use App\Mail\ProfileUpgraded;
 
class UpgradeController extends Component
{
    public $selectedPaymentMethod;
    public $selectedPackage;
    public $selectedDuration;
    public $selectedAmount;
    public $paypalOrderId;
    public $paypalClientId = 'AXEOnZr8asYD0Wav8y-eNaDpv_Vj80sF1wvMXV-iu4V6MMnZWolAo1xfwXJvakd-QSsi7qBHnjZcvFlr';

    public $id, $check;

    public function mount($id)
    {
        $this->id = $id;
        $user = UsersProfile::where('id', $id)->where("user_id", Auth()->user()->id)->first();
        
        if (!$user) {
            return redirect()->route('new.profile');
        }
        
        $this->check = $user->is_active;
    }


    public function render()
    {
        // Get profile first
        $profile = UsersProfile::where('id', $this->id)->where("user_id", Auth()->user()->id)->first();
        
        // Get country from profile's city
        $currentCountry = null;
        if ($profile && $profile->city) {
            $city = \App\Models\City::find($profile->city);
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
        
        return view('livewire.upgrade-controller', compact('packages', 'profile'));
    }


    #[On('processWalletPayment')]
    public function processWalletPayment($packageId = null, $duration = null, $amount = null)
    {
        // Log raw input for debugging
        \Log::info('[UPGRADE] processWalletPayment called with raw params', [
            'packageId_raw' => $packageId,
            'duration_raw' => $duration,
            'amount_raw' => $amount,
            'packageId_type' => gettype($packageId),
        ]);
        
        // Livewire 3 passes dispatched data as array - handle both formats
        if (is_array($packageId) && isset($packageId['packageId'])) {
            $data = $packageId;
            $packageId = $data['packageId'] ?? null;
            $duration = $data['duration'] ?? null;
            $amount = $data['amount'] ?? null;
        }
        
        // Use passed parameters or fall back to class properties
        $selectedPackage = $packageId ?? $this->selectedPackage;
        $selectedDuration = $duration ?? $this->selectedDuration;
        $selectedAmount = $amount ?? $this->selectedAmount;
        
        // If still null, log error
        if (!$selectedPackage || !$selectedDuration || !$selectedAmount) {
            \Log::error('[UPGRADE] Wallet Payment - Missing required parameters', [
                'packageId' => $selectedPackage,
                'duration' => $selectedDuration,
                'amount' => $selectedAmount,
                'class_selectedPackage' => $this->selectedPackage,
                'class_selectedDuration' => $this->selectedDuration,
                'class_selectedAmount' => $this->selectedAmount,
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Payment failed. Missing package details.'
            ]);
            return;
        }
        
        $userEmail = auth()->user()->email ?? 'unknown';
        $userId = auth()->id();
        
        \Log::info('[UPGRADE] Wallet Payment Started', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $this->id,
            'package_id' => $selectedPackage,
            'duration' => $selectedDuration,
            'amount' => $selectedAmount,
            'payment_method' => 'wallet'
        ]);
     
    $profile = UsersProfile::where('id', $this->id)
        ->where('user_id', auth()->user()->id)
        ->first();
    
    if (!$profile) {
        \Log::error('[UPGRADE] Wallet Payment Failed - Profile not found', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $this->id
        ]);
    }
        
    $wallet = auth()->user()->wallet;

    if (!$wallet || $wallet->balance < $selectedAmount) {
        \Log::warning('[UPGRADE] Wallet Payment Failed - Insufficient balance', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'wallet_balance' => $wallet ? $wallet->balance : 0,
            'required_amount' => $selectedAmount
        ]);
        $this->dispatch('showMessage', [
            'type' => 'error',
            'message' => 'Insufficient balance in wallet'
        ]);
        return;
    }

    try {
        DB::transaction(function() use ($wallet, $profile, $selectedPackage, $selectedDuration, $selectedAmount, $userEmail, $userId) {
            \Log::info('[UPGRADE] Wallet Transaction Starting', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id
            ]);
            
            // Create wallet transaction record
            WalletTransaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $wallet->id,
                'amount' => $selectedAmount,
                'type' => 'package_purchase',
                'status' => 'completed',
                'description' => 'Package purchase payment',
                'package_id' => $selectedPackage,
            ]);

            // Deduct from wallet
            $wallet->decrement('balance', $selectedAmount);
            
            // Update profile with package
            $profile->update([
                'is_featured' => true,
                'package_id' => $selectedPackage,
                'package_days' => $selectedDuration,
                'promoted_until' => now()->addDays($selectedDuration),
                'created_at' => now()
            ]);
            
            \Log::info('[UPGRADE] Wallet Transaction Completed - Profile Updated', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id,
                'profile_name' => $profile->name,
                'new_package_id' => $selectedPackage,
                'new_package_days' => $selectedDuration,
                'promoted_until' => now()->addDays($selectedDuration)->toDateTimeString()
            ]);
             
            // Send profile upgrade email if enabled in admin settings
            if (MailSettings::shouldSendEmail('profile_upgrade')) {
                try {
                    $package = Package::find($selectedPackage);
                    $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                    
                    Mail::to(auth()->user()->email)->send(new ProfileUpgraded([
                        'userName' => auth()->user()->name,
                        'profileName' => $profile->name,
                        'packageName' => $package->name,
                        'duration' => $selectedDuration,
                        'profileUrl' => $profileUrl,
                    ]));
                    
                    \Log::info('[UPGRADE] Profile upgrade email sent', ['profile_id' => $profile->id, 'user_email' => $userEmail]);
                } catch (\Exception $e) {
                    \Log::error('[UPGRADE] Profile upgrade email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                }
            }
        });
        
        \Log::info('[UPGRADE] Wallet Payment Success - Dispatching redirect', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $profile->id,
            'profile_name' => $profile->name
        ]);

        $this->dispatch('showMessage', [
            'type' => 'success',
            'message' => 'Package upgraded successfully',
            'name' => $profile->name,
            'id' => $profile->id
        ]);
    } catch (\Exception $e) {
        \Log::error('[UPGRADE] Wallet Payment Exception', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $this->id,
            'package_id' => $selectedPackage ?? 'null',
            'duration' => $selectedDuration ?? 'null',
            'amount' => $selectedAmount ?? 'null',
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        $this->dispatch('showMessage', [
            'type' => 'error',
            'message' => 'Payment failed: ' . $e->getMessage()
        ]);
    }
}

    #[On('processPrimaryPayment')]
    public function processPrimaryPayment($packageId = null, $duration = null, $amount = null, $referenceId = null)
    {
        // Livewire 3 passes dispatched data as array - handle both formats
        if (is_array($packageId) && isset($packageId['packageId'])) {
            $data = $packageId;
            $packageId = $data['packageId'] ?? null;
            $duration = $data['duration'] ?? null;
            $amount = $data['amount'] ?? null;
            $referenceId = $data['referenceId'] ?? null;
        }
        
        $userEmail = auth()->user()->email ?? 'unknown';
        $userId = auth()->id();
        
        \Log::info('[UPGRADE] Primary Gateway Payment Started', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $this->id,
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
                \Log::warning('[UPGRADE] Duplicate Primary Gateway payment attempt blocked', [
                    'reference_id' => $referenceId,
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'existing_transaction_id' => $existingTransaction->id
                ]);
                
                // Still show success since payment was already processed
                $profile = UsersProfile::where('id', $this->id)
                    ->where('user_id', auth()->user()->id)
                    ->first();
                    
                if ($profile) {
                    $this->dispatch('showMessage', [
                        'type' => 'success',
                        'message' => 'Package upgraded successfully via Primary Gateway',
                        'name' => $profile->name,
                        'id' => $profile->id
                    ]);
                }
                return;
            }
        }
        
        // Use passed parameters or fall back to class properties
        $selectedPackage = $packageId ?? $this->selectedPackage;
        $selectedDuration = $duration ?? $this->selectedDuration;
        $selectedAmount = $amount ?? $this->selectedAmount;
        
        $profile = UsersProfile::where('id', $this->id)
            ->where('user_id', auth()->user()->id)
            ->first();
            
        if (!$profile) {
            \Log::error('[UPGRADE] Primary Gateway Payment Failed - Profile not found', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $this->id,
                'reference_id' => $referenceId
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Profile not found'
            ]);
            return;
        }

        \Log::info('[UPGRADE] Primary Gateway Payment - Profile found, starting transaction', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $profile->id,
            'profile_name' => $profile->name,
            'package_id' => $selectedPackage,
            'duration' => $selectedDuration,
            'amount' => $selectedAmount,
            'reference_id' => $referenceId
        ]);

        try {
            DB::transaction(function() use ($profile, $selectedPackage, $selectedDuration, $selectedAmount, $referenceId, $userEmail, $userId) {
                \Log::info('[UPGRADE] Primary Gateway Transaction Starting', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'profile_id' => $profile->id,
                    'reference_id' => $referenceId
                ]);
                
                // Ensure user has a wallet for transaction tracking
                $user = auth()->user();
                $wallet = $user->wallet;
                if (!$wallet) {
                    $wallet = $user->wallet()->create(['balance' => 0]);
                    \Log::info('[UPGRADE] Created new wallet for user', [
                        'user_id' => $userId,
                        'user_email' => $userEmail
                    ]);
                }

                // Create wallet transaction record for tracking
                WalletTransaction::create([
                    'user_id' => auth()->id(),
                    'wallet_id' => $wallet->id,
                    'amount' => $selectedAmount,
                    'type' => 'primary_gateway_payment',
                    'status' => 'completed',
                    'description' => 'Primary Gateway package purchase - Ref: ' . $referenceId,
                    'package_id' => $selectedPackage,
                ]);
                
                \Log::info('[UPGRADE] Primary Gateway Transaction Record Created', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'reference_id' => $referenceId
                ]);
                
                // Update profile with package
                $profile->update([
                    'is_featured' => true,
                    'package_id' => $selectedPackage,
                    'package_days' => $selectedDuration,
                    'promoted_until' => now()->addDays($selectedDuration),
                    'created_at' => now()
                ]);
                
                \Log::info('[UPGRADE] Primary Gateway - Profile Updated Successfully', [
                    'user_id' => $userId,
                    'user_email' => $userEmail,
                    'profile_id' => $profile->id,
                    'profile_name' => $profile->name,
                    'new_package_id' => $selectedPackage,
                    'new_package_days' => $selectedDuration,
                    'promoted_until' => now()->addDays($selectedDuration)->toDateTimeString(),
                    'reference_id' => $referenceId
                ]);
            
                // Send profile upgrade email if enabled in admin settings
                if (MailSettings::shouldSendEmail('profile_upgrade')) {
                    try {
                        $package = Package::find($selectedPackage);
                        $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                        
                        Mail::to(auth()->user()->email)->send(new ProfileUpgraded([
                            'userName' => auth()->user()->name,
                            'profileName' => $profile->name,
                            'packageName' => $package->name,
                            'duration' => $selectedDuration,
                            'profileUrl' => $profileUrl,
                        ]));
                        
                        \Log::info('[UPGRADE] Profile upgrade email sent (Primary Gateway)', ['profile_id' => $profile->id, 'user_email' => $userEmail]);
                    } catch (\Exception $e) {
                        \Log::error('[UPGRADE] Profile upgrade email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                    }
                }
            });
            
            \Log::info('[UPGRADE] Primary Gateway Payment Success - Dispatching redirect', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id,
                'profile_name' => $profile->name,
                'reference_id' => $referenceId
            ]);

            $this->dispatch('showMessage', [
                'type' => 'success',
                'message' => 'Package upgraded successfully via Primary Gateway',
                'name' => $profile->name,
                'id' => $profile->id
            ]);
        } catch (\Exception $e) {
            \Log::error('[UPGRADE] Primary Gateway Payment Exception', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $this->id,
                'reference_id' => $referenceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Payment processing failed. Please contact support.'
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

        // Get profile details
        $profile = UsersProfile::where('id', $this->id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$profile) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Profile not found'
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
            'profile_id' => $this->id
        ];

        // Store order data in session for later verification
        session(['paypal_order_data' => $orderData]);

        // Return order data to the frontend
        $this->dispatch('paypalOrderCreated', [
            'amount' => $this->selectedAmount,
            'currency' => 'EUR',
            'description' => $package->name . ' for ' . $this->selectedDuration . ' days'
        ]);
    }

    #[On('handlePayPalApproval')]
    public function handlePayPalApproval($paypalOrderId = null, $orderId = null)
    {
        // Livewire 3 passes dispatched data as array - handle both formats
        if (is_array($paypalOrderId) && isset($paypalOrderId['orderId'])) {
            $paypalOrderId = $paypalOrderId['orderId'];
        } elseif ($orderId) {
            $paypalOrderId = $orderId;
        }
        
        $userEmail = auth()->user()->email ?? 'unknown';
        $userId = auth()->id();
        
        \Log::info('[UPGRADE] PayPal Payment Started', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'profile_id' => $this->id,
            'paypal_order_id' => $paypalOrderId,
            'payment_method' => 'paypal'
        ]);
        
        // Get the stored order data
        $orderData = session('paypal_order_data');
        
        if (!$orderData) {
            \Log::error('[UPGRADE] PayPal Payment Failed - Order data not found in session', [
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
        
        \Log::info('[UPGRADE] PayPal Payment - Order data found', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'order_data' => $orderData,
            'paypal_order_id' => $paypalOrderId
        ]);

        // Get profile
        $profile = UsersProfile::where('id', $this->id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$profile) {
            \Log::error('[UPGRADE] PayPal Payment Failed - Profile not found', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $this->id,
                'paypal_order_id' => $paypalOrderId
            ]);
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Profile not found'
            ]);
            return;
        }

        try {
            // Update profile with package
            $profile->update([
                'is_featured' => true,
                'package_id' => $orderData['package_id'],
                'package_days' => $orderData['duration'],
                'promoted_until' => now()->addDays($orderData['duration']),
                'created_at' => now()
            ]);
            
            \Log::info('[UPGRADE] PayPal Payment - Profile Updated Successfully', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id,
                'profile_name' => $profile->name,
                'new_package_id' => $orderData['package_id'],
                'new_package_days' => $orderData['duration'],
                'promoted_until' => now()->addDays($orderData['duration'])->toDateTimeString(),
                'paypal_order_id' => $paypalOrderId
            ]);

            // Send profile upgrade email if enabled in admin settings
            if (MailSettings::shouldSendEmail('profile_upgrade')) {
                try {
                    $package = Package::find($orderData['package_id']);
                    $profileUrl = url("/my-profile/{$profile->slug}/{$profile->id}");
                    
                    Mail::to(auth()->user()->email)->send(new ProfileUpgraded([
                        'userName' => auth()->user()->name,
                        'profileName' => $profile->name,
                        'packageName' => $package->name,
                        'duration' => $orderData['duration'],
                        'profileUrl' => $profileUrl,
                    ]));
                    
                    \Log::info('[UPGRADE] Profile upgrade email sent (PayPal)', ['profile_id' => $profile->id, 'user_email' => $userEmail]);
                } catch (\Exception $e) {
                    \Log::error('[UPGRADE] Profile upgrade email failed: ' . $e->getMessage(), ['user_email' => $userEmail]);
                }
            }

            // Create transaction record
            $user = auth()->user();
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
                'description' => 'PayPal payment for package purchase - Order: ' . $paypalOrderId,
                'reference' => $paypalOrderId,
                'package_id' => $orderData['package_id'],
            ]);
            
            \Log::info('[UPGRADE] PayPal Transaction Record Created', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'paypal_order_id' => $paypalOrderId,
                'amount' => $orderData['amount']
            ]);

            // Clear session data
            session()->forget('paypal_order_data');
            
            \Log::info('[UPGRADE] PayPal Payment Success - Dispatching redirect', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $profile->id,
                'profile_name' => $profile->name,
                'paypal_order_id' => $paypalOrderId
            ]);

            // Send success message
            $this->dispatch('showMessage', [
                'type' => 'success',
                'message' => 'Package upgraded successfully',
                'name' => $profile->name,
                'id' => $profile->id
            ]);
        } catch (\Exception $e) {
            \Log::error('[UPGRADE] PayPal Payment Exception', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'profile_id' => $this->id,
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Payment processing failed. Please contact support.'
            ]);
        }
    }


}
