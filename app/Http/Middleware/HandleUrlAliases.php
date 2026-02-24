<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UrlAlias;
use Symfony\Component\HttpFoundation\Response;

class HandleUrlAliases
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentPath = trim($request->getPathInfo(), '/');
        
        // Skip if empty path (homepage) or admin routes
        if (empty($currentPath) || str_starts_with($currentPath, 'admin/')) {
            return $next($request);
        }
        
        // Check if current URL is a custom alias
        $alias = UrlAlias::findByCustomUrl($currentPath);
        
        if ($alias) {
            // Handle different redirect types
            switch ($alias->redirect_type) {
                case '301':
                case '302':
                    // Redirect to base pattern
                    $redirectCode = (int) $alias->redirect_type;
                    return redirect('/' . $alias->base_pattern, $redirectCode);
                    
                case 'canonical':
                    // Rewrite the request path internally (no redirect)
                    $request->server->set('REQUEST_URI', '/' . $alias->base_pattern);
                    $request->server->set('PATH_INFO', '/' . $alias->base_pattern);
                    
                    // Add canonical URL to view data
                    view()->share('canonicalUrl', url($alias->base_pattern));
                    view()->share('isAliasUrl', true);
                    view()->share('originalAlias', $alias);
                    break;
            }
        }
        
        return $next($request);
    }
}
