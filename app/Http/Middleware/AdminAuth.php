<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and is an admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            // Redirect to admin login page
            return redirect()->route('admin.login')->with('error', 'Please login to access admin panel.');
        }

        return $next($request);
    }
}
