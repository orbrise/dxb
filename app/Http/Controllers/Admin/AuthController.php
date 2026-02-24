<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    public function index() {
        if(Auth::check()){
            return redirect()->route("admin.dashboard");
        } 
        return view('admin.auth.login');
    }

    public function checkLogin()
    {   
        if(Auth::check()){
            return redirect()->route("admin.dashboard");
        } else {
            return redirect()->route('admin.login');
        }
    }

    public function loginPost(Request $req){
        
    
        if(Auth::attempt(['email' => $req->email, 'password' => $req->password, 'is_admin' => 1])){
            return redirect()->route('admin.dashboard');
        } else {
            return back()->with("error", "Username/Password does't matched");
        }
    }

     public function logout(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidate and regenerate session (for web)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login or home page
        return redirect()->route("admin.login")->with('message', 'You have been logged out.');
    }
}
