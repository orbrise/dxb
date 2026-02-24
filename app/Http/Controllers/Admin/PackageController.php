<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageCountryPrice;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Listing;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('countryPrices.country')->latest()->get();
        $countries = \App\Models\Country::orderBy('nicename')->get();
        
        // Get current country with fallback
        $currentCountry = function_exists('getCurrentCountry') 
            ? getCurrentCountry() 
            : \App\Models\Country::where('nicename', 'United Arab Emirates')->first();
        
        return view('admin.packages', compact('packages', 'countries', 'currentCountry'));
    }

    public function store(Request $request)
    {
        // Debug: Log incoming request data
        \Log::info('Package store request:', $request->all());
        
        $isGlobal = $request->boolean('is_global');
        
        $rules = [
            'name' => 'required|max:50',
            'tagline' => 'required|max:50',
            'description' => 'nullable',
            'is_global' => 'boolean',
        ];
        
        if ($isGlobal) {
            // For global packages, require tiers but not country_prices
            $rules['tiers'] = 'required|array|min:1';
            $rules['tiers.*.days'] = 'required|integer';
            $rules['tiers.*.price'] = 'required|numeric';
        } else {
            // For country-specific packages
            $rules['country_prices'] = 'required|array|min:1';
            $rules['country_prices.*.country_id'] = 'required|exists:countries,id';
            $rules['country_prices.*.tiers'] = 'required|array|min:1';
            $rules['country_prices.*.tiers.*.days'] = 'required|integer';
            $rules['country_prices.*.tiers.*.price'] = 'required|numeric';
        }
        
        $request->validate($rules);
    
        $package = Package::create([
            'name' => $request->name,
            'tagline' => $request->tagline,
            'description' => $request->description,
            'is_global' => $isGlobal,
            'price_tiers' => $isGlobal ? json_encode($request->tiers) : json_encode([])
        ]);

        // Save country-specific prices only if not global
        if (!$isGlobal && $request->country_prices) {
            foreach ($request->country_prices as $countryPrice) {
                PackageCountryPrice::create([
                    'package_id' => $package->id,
                    'country_id' => $countryPrice['country_id'],
                    'price_tiers' => json_encode($countryPrice['tiers'])
                ]);
            }
        }
    
        return response()->json([
            'success' => true,
            'package' => $package->load('countryPrices.country')
        ]);
    }

public function update(Request $request, $id = null)
{
    // Debug: Log incoming request data
    \Log::info('Package update request:', $request->all());
    
    // Support both old style (rid in request) and new RESTful style (id in route)
    $packageId = $id ?? $request->rid;
    $isGlobal = $request->boolean('is_global');
    
    $rules = [
        'name' => 'required|max:50',
        'tagline' => 'required|max:50',
        'description' => 'nullable',
        'is_global' => 'boolean',
    ];
    
    if ($isGlobal) {
        // For global packages, require tiers but not country_prices
        $rules['tiers'] = 'required|array|min:1';
        $rules['tiers.*.days'] = 'required|integer';
        $rules['tiers.*.price'] = 'required|numeric';
    } else {
        // For country-specific packages
        $rules['country_prices'] = 'required|array|min:1';
        $rules['country_prices.*.country_id'] = 'required|exists:countries,id';
        $rules['country_prices.*.tiers'] = 'required|array|min:1';
        $rules['country_prices.*.tiers.*.days'] = 'required|integer';
        $rules['country_prices.*.tiers.*.price'] = 'required|numeric';
    }
    
    $request->validate($rules);

    $package = Package::findOrFail($packageId);
    $package->update([
        'name' => $request->name,
        'tagline' => $request->tagline,
        'description' => $request->description,
        'is_global' => $isGlobal,
        'price_tiers' => $isGlobal ? json_encode($request->tiers) : json_encode([])
    ]);

    // Delete existing country prices and recreate
    $package->countryPrices()->delete();
    
    if (!$isGlobal && $request->country_prices) {
        foreach ($request->country_prices as $countryPrice) {
            PackageCountryPrice::create([
                'package_id' => $package->id,
                'country_id' => $countryPrice['country_id'],
                'price_tiers' => json_encode($countryPrice['tiers'])
            ]);
        }
    }

    return response()->json([
        'success' => true,
        'package' => $package->load('countryPrices.country')
    ]);
}

    public function delete(Request $request)
    {
        Package::findOrFail($request->rid)->delete();
        return 'success';
    }


    public function getPackage($id)
    {
        // Get current country from domain with fallback
        $currentCountry = function_exists('getCurrentCountry') 
            ? getCurrentCountry() 
            : \App\Models\Country::where('nicename', 'United Arab Emirates')->first();
        
        // Load package with country-specific pricing for current country
        $package = Package::with(['countryPrices' => function($query) use ($currentCountry) {
            $query->where('country_id', $currentCountry->id)->with('country');
        }])->findOrFail($id);
        
        return response()->json($package);
    }
    
    public function getPackagesByCountry($countryId)
    {
        // Get only country-specific packages (exclude global packages)
        $countryPackages = Package::with(['countryPrices' => function($query) use ($countryId) {
            $query->where('country_id', $countryId);
        }])
        ->where('is_global', false)
        ->whereHas('countryPrices', function($query) use ($countryId) {
            $query->where('country_id', $countryId);
        })
        ->latest()
        ->get();
        
        return response()->json(['packages' => $countryPackages]);
    }

public function getAllPackages()
{
    return Package::latest()->get();
}


public function wallet(Request $request)
    {
        $allowed = [10, 25, 50, 100];
        $perPage = (int) $request->get('perPage', 10);
        if (!in_array($perPage, $allowed)) {
            $perPage = 10;
        }

        $users = User::all();
        $transactions = WalletTransaction::with('wallet.user')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
       
        return view('admin.wallet', compact('users', 'transactions'));
    }

    public function topup(Request $request)
    {
        // Find or create wallet for the user
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $request->user_id],
            ['balance' => 0]
        );
    
        // Update wallet balance
        $wallet->balance += $request->amount;
        $wallet->save();
    
        // Create transaction record
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'type' => 'credit',
            'description' => $request->note ?? 'Admin topup',
            'status' => 'completed'
        ]);
    
        return response()->json(['success' => true]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_email' => 'required|email',
            'to_email' => 'required|email|different:from_email',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255'
        ]);

        try {
            // Find sender and receiver users
            $fromUser = User::where('email', $request->from_email)->first();
            $toUser = User::where('email', $request->to_email)->first();

            if (!$fromUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sender user not found with email: ' . $request->from_email
                ], 404);
            }

            if (!$toUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receiver user not found with email: ' . $request->to_email
                ], 404);
            }

            // Get or create sender wallet
            $fromWallet = Wallet::firstOrCreate(
                ['user_id' => $fromUser->id],
                ['balance' => 0]
            );

            // Check if sender has sufficient balance
            if ($fromWallet->balance < $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance. Current balance: AED ' . number_format($fromWallet->balance, 2)
                ], 400);
            }

            // Get or create receiver wallet
            $toWallet = Wallet::firstOrCreate(
                ['user_id' => $toUser->id],
                ['balance' => 0]
            );

            // Perform the transfer
            $fromWallet->balance -= $request->amount;
            $fromWallet->save();

            $toWallet->balance += $request->amount;
            $toWallet->save();

            // Create debit transaction for sender
            WalletTransaction::create([
                'wallet_id' => $fromWallet->id,
                'amount' => $request->amount,
                'type' => 'debit',
                'description' => $request->note ?? 'Admin transfer to ' . $toUser->name,
                'status' => 'completed'
            ]);

            // Create credit transaction for receiver
            WalletTransaction::create([
                'wallet_id' => $toWallet->id,
                'amount' => $request->amount,
                'type' => 'credit',
                'description' => $request->note ?? 'Admin transfer from ' . $fromUser->name,
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully',
                'data' => [
                    'from_user' => $fromUser->name,
                    'to_user' => $toUser->name,
                    'amount' => $request->amount,
                    'from_balance' => number_format($fromWallet->balance, 2),
                    'to_balance' => number_format($toWallet->balance, 2)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with this email address'
            ], 404);
        }

        // Get user's current wallet balance
        $wallet = Wallet::where('user_id', $user->id)->first();
        $currentBalance = $wallet ? $wallet->balance : 0;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'current_balance' => number_format($currentBalance, 2),
                'avatar' => $user->avatar ?? null
            ]
        ]);
    }

    public function getUserTransactions($userId)
{
    $transactions = WalletTransaction::whereHas('wallet', function($query) use ($userId) {
        $query->where('user_id', $userId);
    })->with('wallet.user')->latest()->get();
    
    return response()->json($transactions);
}

public function listings(Request $request)
{
    // Allowable per-page sizes
    $allowed = [10, 25, 50, 100];
    $perPage = (int) $request->get('perPage', 10);
    if (!in_array($perPage, $allowed)) {
        $perPage = 10;
    }

    $listings = Listing::orderByDesc('id')->paginate($perPage)->withQueryString();
    return view('admin.listings.index', compact('listings'));
}

public function createListing()
{
    return view('admin.listings.create');
}

public function storeListing(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255'
    ]);

    Listing::create($validated);
    return redirect()->route('admin.listings.index')->with('success', 'Listing created successfully');
}

public function editListing(Listing $listing)
{
    return view('admin.listings.edit', compact('listing'));
}

public function updateListing(Request $request, Listing $listing)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255'
    ]);

    $listing->update($validated);
    return redirect()->route('admin.listings.index')->with('success', 'Listing updated successfully');
}

public function destroyListing(Listing $listing)
{
    $listing->delete();
    return redirect()->route('admin.listings.index')->with('success', 'Listing deleted successfully');
}

// User wallet view
// public function wallet()
// {
//     $wallet = auth()->user()->wallet;
//     $transactions = $wallet->transactions()->latest()->get();
//     return view('admin.wallet', compact('wallet', 'transactions'));
// }

// // Credit card topup
// public function topupWallet(Request $request)
// {
//     // Process credit card payment
//     $payment = $this->processPayment($request);
    
//     if($payment->success) {
//         $wallet = auth()->user()->wallet;
//         $wallet->balance += $request->amount;
//         $wallet->save();
        
//         WalletTransaction::create([
//             'wallet_id' => $wallet->id,
//             'amount' => $request->amount,
//             'type' => 'credit',
//             'description' => 'Card topup',
//             'payment_method' => 'card',
//             'status' => 'completed'
//         ]);
//     }
// }

// // Package purchase from wallet
// public function purchasePackage(Request $request)
// {
//     $wallet = auth()->user()->wallet;
//     $package = Package::find($request->package_id);
    
//     if($wallet->balance >= $package->price) {
//         $wallet->balance -= $package->price;
//         $wallet->save();
        
//         // Create transaction record
//         WalletTransaction::create([
//             'wallet_id' => $wallet->id,
//             'amount' => $package->price,
//             'type' => 'debit',
//             'description' => "Purchase {$package->name}",
//             'status' => 'completed'
//         ]);
        
//         // Assign package to user
//         // Add package assignment logic here
//     }
// }

    public function globalPackages()
    {
        $packages = Package::where('is_global', true)->latest()->get();
        return view('admin.global-packages', compact('packages'));
    }

    public function show($id)
    {
        $package = Package::findOrFail($id);
        return response()->json($package);
    }

    public function destroy($id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->json(['success' => true, 'message' => 'Package deleted successfully']);
    }

}
