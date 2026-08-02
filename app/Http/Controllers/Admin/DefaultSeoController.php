<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DefaultSeoSetting;
use Illuminate\Support\Facades\Route as RouteFacade;

class DefaultSeoController extends Controller
{
    /**
     * Display a listing of the default SEO settings.
     */
    public function index()
    {
        $defaultSeoSettings = DefaultSeoSetting::orderBy('priority', 'desc')
                                             ->orderBy('created_at', 'desc')
                                             ->get();

        return view('admin.default-seo.index', compact('defaultSeoSettings'));
    }

    /**
     * List frontend routes and show which ones have a page-specific SEO row.
     */
    public function pages()
    {
        $routes = $this->getSeoableRoutes();

        $settings = DefaultSeoSetting::whereIn('name', $routes->pluck('path'))
            ->get()
            ->keyBy('name');

        return view('admin.default-seo.pages', compact('routes', 'settings'));
    }

    /**
     * Enumerate GET frontend routes that have a fixed URI (no route parameters).
     * These are the routes an admin can target with a page-specific SEO override.
     * Homepage is excluded because it already has the "homepage" default context.
     */
    private function getSeoableRoutes()
    {
        // Whole segment matches (URI is exactly one of these OR starts with "<prefix>/").
        // Use this for anything with a clean URI boundary (admin/, api/, page/{slug}, etc.).
        $excludedSegmentPrefixes = [
            'admin', 'api', 'livewire', 'sanctum', 'broadcasting',
            'sitemap', 'sitemaps', '_ignition', '_debugbar',
            'up', 'storage', 'horizon', 'telescope', 'webhook', 'amp',
            'auth', 'csrf-refresh',
        ];

        // Raw string prefixes — matches anything starting with these characters,
        // regardless of segment boundary. Use for dev/test scaffolding and non-HTML files.
        $excludedStringPrefixes = [
            'test-', 'demo-', 'sitemap.',
        ];

        return collect(RouteFacade::getRoutes())
            ->filter(fn($route) => in_array('GET', $route->methods()))
            ->filter(fn($route) => !str_contains($route->uri(), '{'))
            ->filter(function ($route) use ($excludedSegmentPrefixes, $excludedStringPrefixes) {
                $uri = trim($route->uri(), '/');
                if ($uri === '' || $uri === '/') {
                    return false; // homepage handled separately by "homepage" context
                }
                foreach ($excludedSegmentPrefixes as $prefix) {
                    if ($uri === $prefix || str_starts_with($uri, $prefix . '/')) {
                        return false;
                    }
                }
                foreach ($excludedStringPrefixes as $prefix) {
                    if (str_starts_with($uri, $prefix)) {
                        return false;
                    }
                }
                return true;
            })
            ->map(fn($route) => (object) [
                'path' => trim($route->uri(), '/'),
                'name' => $route->getName(),
            ])
            ->unique('path')
            ->sortBy('path')
            ->values();
    }

    /**
     * Show the form for creating a new default SEO setting.
     */
    public function create()
    {
        return view('admin.default-seo.create');
    }

    /**
     * Store a newly created default SEO setting in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:default_seo_settings,name',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'content' => 'nullable|string',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        DefaultSeoSetting::create([
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'content' => $request->content,
            'priority' => $request->priority,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('default-seo.index')
                        ->with('success', 'Default SEO setting created successfully!');
    }

    /**
     * Display the specified default SEO setting.
     */
    public function show(DefaultSeoSetting $defaultSeoSetting)
    {
        return view('admin.default-seo.show', compact('defaultSeoSetting'));
    }

    /**
     * Show the form for editing the specified default SEO setting.
     */
    public function edit(DefaultSeoSetting $defaultSeoSetting)
    {
        return view('admin.default-seo.edit', compact('defaultSeoSetting'));
    }

    /**
     * Update the specified default SEO setting in storage.
     */
    public function update(Request $request, DefaultSeoSetting $defaultSeoSetting)
    {
        $request->validate([
            'name' => 'required|string|unique:default_seo_settings,name,' . $defaultSeoSetting->id,
            'title' => 'required|string',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'content' => 'nullable|string',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $defaultSeoSetting->update([
            'name' => $request->name,
            'title' => $request->title,
            'description' => $request->description,
            'keywords' => $request->keywords,
            'content' => $request->content,
            'priority' => $request->priority,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('default-seo.index')
                        ->with('success', 'Default SEO setting updated successfully!');
    }

    /**
     * Remove the specified default SEO setting from storage.
     */
    public function destroy(DefaultSeoSetting $defaultSeoSetting)
    {
        $defaultSeoSetting->delete();

        return redirect()->route('default-seo.index')
                        ->with('success', 'Default SEO setting deleted successfully!');
    }
}
