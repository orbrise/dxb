<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        
        // Deactivate the user account instead of deleting
        $user->update([
            'status' => 'deactivated'
        ]);

        // Log out the user
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deactivated successfully.');
    }
}
