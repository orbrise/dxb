<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MinifyHtml
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only minify HTML responses
        if ($response->headers->get('Content-Type') === 'text/html; charset=UTF-8' ||
            str_contains($response->headers->get('Content-Type', ''), 'text/html')) {

            $content = $response->getContent();

            // Simple HTML minification - remove unnecessary whitespace
            $content = $this->minifyHtml($content);

            $response->setContent($content);
        }

        return $response;
    }

    protected function minifyHtml(string $html): string
    {
        // Remove HTML comments (except IE conditional comments)
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);

        // Remove whitespace between tags
        $html = preg_replace('/>\s+</', '><', $html);

        // Remove whitespace at the beginning and end of lines
        $html = preg_replace('/^\s+|\s+$/m', '', $html);

        // Remove multiple consecutive line breaks
        $html = preg_replace('/\n\s*\n/', "\n", $html);

        // Don't remove whitespace inside <pre>, <textarea>, <script>, <style> tags
        // This is a simple implementation - a full minifier would be more complex

        return $html;
    }
}