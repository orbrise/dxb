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
        // Pull <script> and <style> blocks out BEFORE minifying — the
        // whitespace rules below use \s which matches \n, and that
        // greedily eats newlines inside JS, collapsing a line comment
        // with the next statement (e.g. `// foo\nconst x = {` becomes
        // `// fooconst x = {` which breaks the whole script).
        $placeholders = [];
        $html = preg_replace_callback(
            '/<(script|style)\b[^>]*>[\s\S]*?<\/\1>/i',
            function ($m) use (&$placeholders) {
                $key = '__MIN_PH_' . count($placeholders) . '__';
                $placeholders[$key] = $m[0];
                return $key;
            },
            $html
        );

        // Remove HTML comments (except IE conditional comments)
        $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);

        // Remove whitespace between tags
        $html = preg_replace('/>\s+</', '><', $html);

        // Strip leading/trailing spaces/tabs on each line — but NOT newlines
        // (\s would also match \n and merge adjacent lines together).
        $html = preg_replace('/^[ \t]+|[ \t]+$/m', '', $html);

        // Collapse 3+ consecutive newlines down to 2 (keeps blank-line
        // separators between blocks but strips excess).
        $html = preg_replace("/\n{3,}/", "\n\n", $html);

        // Restore the script/style blocks verbatim.
        if ($placeholders) {
            $html = strtr($html, $placeholders);
        }

        return $html;
    }
}