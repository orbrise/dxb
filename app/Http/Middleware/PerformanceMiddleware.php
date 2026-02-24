<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Add security headers
        $response = $next($request);
        
        // Add performance headers
        $response->headers->set('X-Response-Time', round((microtime(true) - $startTime) * 1000, 2) . 'ms');
        
        // Add caching headers for static content
        if ($request->is('assets/*') || $request->is('storage/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 year
        }
        
        // Compress response if it's HTML/CSS/JS
        if ($this->shouldCompress($response)) {
            $content = $response->getContent();
            if ($content && function_exists('gzencode')) {
                $compressed = gzencode($content, 9);
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', strlen($compressed));
                }
            }
        }
        
        // Add security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        return $response;
    }
    
    private function shouldCompress(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        
        return str_contains($contentType, 'text/html') ||
               str_contains($contentType, 'text/css') ||
               str_contains($contentType, 'application/javascript') ||
               str_contains($contentType, 'application/json');
    }
}
