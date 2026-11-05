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

            // Simple HTML minification - remove unnecessary whitespace.
            // null = a regex hit a PCRE limit; serve the page unminified
            // rather than blank (preg_* returns null, which the later
            // calls turned into an empty body).
            $content = $this->minifyHtml($response->getContent());

            if ($content !== null) {
                $response->setContent($content);
            }
        }

        return $response;
    }

    protected function minifyHtml(string $html): ?string
    {
        // Pull <script> and <style> blocks out BEFORE minifying — the
        // whitespace rules below use \s which matches \n, and that
        // greedily eats newlines inside JS, collapsing a line comment
        // with the next statement (e.g. `// foo\nconst x = {` becomes
        // `// fooconst x = {` which breaks the whole script).
        //
        // Same match as the lazy `[\s\S]*?<\/\1>`, written as possessive runs
        // of non-"<" text so it scans in linear time — the lazy form ran out
        // of PCRE's backtrack limit on ~1MB inline blocks (e.g. the 47k-city
        // JSON on /admin/profiles).
        $placeholders = [];
        $html = preg_replace_callback(
            '/<(script|style)\b[^>]*+>[^<]*+(?:<(?!\/\1>)[^<]*+)*+<\/\1>/i',
            function ($m) use (&$placeholders) {
                $key = '__MIN_PH_' . count($placeholders) . '__';
                $placeholders[$key] = $m[0];
                return $key;
            },
            $html
        );

        // Applied in order; preg_replace returns null if any step fails.
        $html = $html === null ? null : preg_replace([
            // Remove HTML comments (except IE conditional comments)
            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s',
            // Remove whitespace between tags
            '/>\s+</',
            // Strip leading/trailing spaces/tabs on each line — but NOT newlines
            // (\s would also match \n and merge adjacent lines together).
            '/^[ \t]+|[ \t]+$/m',
            // Collapse 3+ consecutive newlines down to 2 (keeps blank-line
            // separators between blocks but strips excess).
            "/\n{3,}/",
        ], ['', '><', '', "\n\n"], $html);

        if ($html === null) {
            return null;
        }

        // Restore the script/style blocks verbatim.
        if ($placeholders) {
            $html = strtr($html, $placeholders);
        }

        return $html;
    }
}