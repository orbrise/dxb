<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersProfile;
use App\Models\Package;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('profiles');

        // Filter by ID
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filter by Name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by Email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Get all users (DataTables handles pagination client-side)
        $users = $query->orderBy('id', 'desc')->get();
        $packages = Package::all();
        $countries = Country::orderBy('nicename')->get();
        
        return view('admin.users', compact('users', 'packages', 'countries'));
    }

    // Add New User
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => date('Y-m-d'),
            'verified' => 0,
            'type' => 1,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }

    // Get Single User
    public function getUser($id)
    {
        $user = User::findOrFail($id);
        
        // Add password hash info for admin view (truncated for display)
        $userData = $user->toArray();
        $userData['password_hash'] = $user->password ? substr($user->password, 0, 20) . '...' : null;
        
        return response()->json($userData);
    }
 
    // Update User
    

    // Delete User 
    public function deleteUser(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->profiles()->delete(); // Delete associated profiles
        $user->delete();

        return response()->json(['success' => 'User deleted successfully']);
    }

    // Send Verification Email
    public function sendVerificationEmail(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->id);

        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'User email is already verified'
            ], 400);
        }

        try {
            // Generate verification code if not exists
            if (!$user->verification_code) {
                $user->verification_code = \Str::random(60);
                $user->save();
            }

            // Prepare mail data
            $mailData = [
                'name' => $user->name,
                'email' => $user->email,
                'random' => $user->verification_code
            ];

            // Send verification email
            \Mail::send('emails.newuser', ['mailData' => $mailData], function($message) use ($user) {
                $message->to($user->email, $user->name);
                $message->subject('Activate Your Account - Email Verification');
            });

            \Log::info('Verification email sent to user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'sent_by_admin' => auth()->id()
            ]);

            return response()->json([
                'success' => 'Verification email sent successfully to ' . $user->email
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Get Profiles for a User
    public function getProfiles(Request $request)
    {
        try {
            \Log::info('Getting profiles for user_id: ' . $request->user_id);
            
            $profiles = UsersProfile::where('user_id', $request->user_id)
                ->with(['getcity', 'getgender'])
                ->get();
            
            \Log::info('Found ' . $profiles->count() . ' profiles');
            
            if ($profiles->isEmpty()) {
                return '<tr><td colspan="5" class="text-center">No profiles found for this user</td></tr>';
            }
            
            $html = '';
 
            foreach ($profiles as $profile) {
                // Get city name
                $cityName = $profile->getcity ? $profile->getcity->name : 'N/A';
                
                // Get gender name
                $genderName = $profile->getgender ? $profile->getgender->name : 'N/A';
                
                \Log::info('Profile: ' . $profile->name . ', City: ' . $cityName . ', Gender: ' . $genderName);
                
                $html .= '
                <tr id="profile' . $profile->id . '">
                    <td>' . htmlspecialchars($profile->name) . '</td>
                    <td>' . htmlspecialchars($cityName) . '</td>
                    <td>' . htmlspecialchars($profile->phone ?? 'N/A') . '</td>
                    <td>' . htmlspecialchars(ucfirst($genderName)) . '</td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-profile" data-id="' . $profile->id . '">Edit</button>
                        <button class="btn btn-danger btn-sm delete-profile" data-id="' . $profile->id . '">Delete</button>
                         <button class="btn btn-primary btn-sm assign-package" 
                data-id="' . $profile->id . '" 
                data-featured="' . $profile->is_featured . '"
                data-package="' . $profile->package_id . '">
            Assign Package
        </button>
                        
                    </td>
                </tr>';
            }

            \Log::info('Returning HTML with length: ' . strlen($html));
            return $html;
        } catch (\Exception $e) {
            \Log::error('Error in getProfiles: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return '<tr><td colspan="5" class="text-center text-danger">Error: ' . $e->getMessage() . '</td></tr>';
        }
    }

    // Add Profile
    public function storeProfile(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        UsersProfile::create($request->all());

        return response()->json(['success' => 'Profile added successfully!']);
    }



    // Delete Profile
   public function deleteProfile(Request $request)
{
    $request->validate(['id' => 'required|exists:users_profiles,id']);

    $profile = UsersProfile::findOrFail($request->id);
    $profile->delete();

    return response()->json(['success' => 'Profile deleted successfully!']);
}

public function getProfile(Request $request)
{

    $profile = UsersProfile::findOrFail($request->id);
    return response()->json($profile);
}

// Update Profile
public function updateProfile(Request $request)
{
    $request->validate([
        'id' => 'required|exists:users_profiles,id',
        'name' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'gender' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
    ]);

    $profile = UsersProfile::findOrFail($request->id);
    $profile->update($request->except('id'));

    return response()->json(['success' => 'Profile updated successfully!']);
}


public function assignPackage(Request $request)
{
    $request->validate([
        'profile_id' => 'required|exists:users_profiles,id',
        'package_id' => 'required|exists:packages,id',
    ]);

    $profile = UsersProfile::findOrFail($request->profile_id);

    UsersProfile::where('id', $profile->id)->update([
        'package_id' => $request->package_id,
        'is_featured' => 1,
        'created_at' => now() // Update created_at so profile shows on top in listings
    ]);

    return response()->json(['success' => 'Package assigned successfully']);
}

public function updateUser(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'type' => 'nullable|integer',
        'status' => 'nullable|in:pending,active,suspended',
        'verified' => 'nullable|boolean',
        'country_code' => 'nullable|string|max:10',
        'phone' => 'nullable|string|max:20',
        'about' => 'nullable|string',
        'new_password' => 'nullable|string|min:6',
    ]);

    $user = User::findOrFail($id);
    
    $updateData = [
        'name' => $request->name,
        'email' => $request->email
    ];
    
    // Add optional fields if they exist
    if ($request->has('type')) {
        $updateData['type'] = $request->type;
    }
    if ($request->has('status')) {
        $updateData['status'] = $request->status;
    }
    if ($request->has('verified')) {
        $updateData['verified'] = $request->verified;
        // Update email_verified_at if verifying
        if ($request->verified == 1 && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        }
    }
    if ($request->has('country_code')) {
        // Store country code without + prefix for consistency
        $countryCode = $request->country_code;
        $updateData['country_code'] = ltrim($countryCode, '+');
    }
    if ($request->has('phone')) {
        $updateData['phone'] = $request->phone;
    }
    if ($request->has('about')) {
        $updateData['about'] = $request->about;
    }
    
    // Handle password change
    if ($request->filled('new_password')) {
        $updateData['password'] = \Hash::make($request->new_password);
    }
    
    $user->update($updateData);

    $message = 'User updated successfully';
    if ($request->filled('new_password')) {
        $message .= '. Password has been changed.';
    }

    return response()->json(['success' => $message]);
}

/**
 * Toggle user status between active and pending
 */
public function toggleUserStatus(Request $request)
{
    $request->validate([
        'id' => 'required|exists:users,id',
        'status' => 'required|in:active,pending'
    ]);

    $user = User::findOrFail($request->id);
    $user->status = $request->status;
    
    // When activating user, also verify their email
    if ($request->status === 'active') {
        $user->verified = 1;
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
    } else {
        // When deactivating, unverify email
        $user->verified = 0;
    }
    
    $user->save();

    return response()->json([
        'success' => 'User status updated to ' . $request->status . ' successfully',
        'status' => $user->status,
        'verified' => $user->verified
    ]);
}

/**
 * Impersonate a user - allows admin to login as any user
 */
public function impersonate(Request $request)
{
    // Check if the current user is admin
    if (!auth()->check() || !auth()->user()->is_admin) {
        return redirect()->route('admin.login')->with('error', 'Unauthorized access');
    }

    $userId = $request->user_id;
    $profileId = $request->profile_id; // Optional profile ID if impersonating via profile
    $userToImpersonate = User::findOrFail($userId);

    // Store the admin user ID and generate a secure token
    $impersonationToken = bin2hex(random_bytes(32));
    $sessionData = [
        'admin_impersonating' => auth()->id(),
        'impersonated_user' => $userToImpersonate->id,
        'impersonation_token' => $impersonationToken,
        'impersonation_started' => now()->toDateTimeString()
    ];
    
    // If impersonating via profile, store the profile ID for potential redirect
    if ($profileId) {
        $sessionData['impersonated_via_profile'] = $profileId;
    }
    
    session($sessionData);
    
    // Log in as the target user
    auth()->login($userToImpersonate);
    
    // Log this action for security audit
    \Log::info('Admin impersonation started', [
        'admin_id' => session('admin_impersonating'),
        'target_user_id' => $userToImpersonate->id,
        'target_user_email' => $userToImpersonate->email,
        'via_profile_id' => $profileId,
        'timestamp' => now(),
        'ip' => $request->ip()
    ]);
    
    // Determine redirect URL
    $redirectUrl = '/';
    if ($profileId) {
        // Try to find a profile-specific URL or redirect to user dashboard
        $profile = UsersProfile::find($profileId);
        if ($profile && $profile->slug) {
            // If profile has a slug, redirect to profile page
            $redirectUrl = "/profile/{$profile->slug}/{$profile->id}";
        } else {
            // Otherwise redirect to user account page
            $redirectUrl = "/my-account";
        }
    }
    
    // Redirect to the main site (user area) with a banner indicating impersonation
    return redirect($redirectUrl)->with('impersonating', [
        'user_name' => $userToImpersonate->name,
        'user_email' => $userToImpersonate->email,
        'admin_name' => User::find(session('admin_impersonating'))->name,
        'via_profile' => $profileId ? UsersProfile::find($profileId)->name : null
    ]);
}

/**
 * Exit impersonation and return to admin account
 */
public function exitImpersonation(Request $request)
{
    // Check if we're actually impersonating
    if (!session('admin_impersonating')) {
        return redirect('/')->with('error', 'Not currently impersonating any user');
    }

    // Get the original admin user
    $adminUser = User::findOrFail(session('admin_impersonating'));
    $impersonatedUserId = session('impersonated_user');
    
    // Log the exit action for security audit
    \Log::info('Admin impersonation ended', [
        'admin_id' => session('admin_impersonating'),
        'impersonated_user_id' => $impersonatedUserId,
        'via_profile_id' => session('impersonated_via_profile'),
        'duration' => session('impersonation_started') ? 
            now()->diffInMinutes(session('impersonation_started')) . ' minutes' : 'unknown',
        'timestamp' => now(),
        'ip' => $request->ip()
    ]);
    
    // Clear impersonation sessions
    session()->forget(['admin_impersonating', 'impersonated_user', 'impersonation_token', 'impersonation_started', 'impersonated_via_profile']);
    
    // Log back in as admin
    auth()->login($adminUser);
    
    // Redirect back to admin panel
    return redirect()->route('admin.users')->with('success', 'Exited impersonation mode successfully');
}


}
