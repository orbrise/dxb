<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UrlRedirect;

class UrlRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip admin routes and API routes
        if ($request->is('admin/*') || $request->is('api/*')) {
            return $next($request);
        }

        // Get the current URL path
        $currentPath = $request->getPathInfo();
        $currentUrl = $request->url();
        $currentFullUrl = $request->fullUrl();
        
        // Remove leading slash for comparison
        $currentPath = ltrim($currentPath, '/');
        
        // Check if there's a redirect for this URL
        $redirect = UrlRedirect::where('is_active', true)
            ->where(function($query) use ($currentPath, $currentUrl, $currentFullUrl) {
                $query->where('link_from', $currentPath)
                      ->orWhere('link_from', '/' . $currentPath)
                      ->orWhere('link_from', $currentUrl)
                      ->orWhere('link_from', $currentFullUrl);
            })
            ->first();

        if ($redirect) {
            $redirectTo = $redirect->link_to;
            
            // If it's not a direct link, check if it needs domain prepending
            if (!$redirect->is_direct_link) {
                // If the redirect doesn't start with http, prepend the domain
                if (!str_starts_with($redirectTo, 'http')) {
                    $redirectTo = url($redirectTo);
                }
            }
            
            // Perform the redirect (301 permanent redirect)
            return redirect($redirectTo, 301);
        }

        // Add debug header to verify middleware is running
        $response = $next($request);
        $response->headers->set('X-Redirect-Check', 'No active redirect found');
        return $response;
    }
}
