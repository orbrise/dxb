<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UrlAlias;
use Illuminate\Http\Request;

class UrlAliasController extends Controller
{
    public function index()
    {
        $aliases = UrlAlias::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.url-aliases.index', compact('aliases'));
    }

    public function create()
    {
        return view('admin.url-aliases.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'custom_url' => 'required|string|unique:url_aliases,custom_url|regex:/^[a-zA-Z0-9_-]+$/',
            'base_pattern' => 'required|string',
            'redirect_type' => 'required|in:301,302,canonical',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        // Clean URLs
        $customUrl = trim($request->custom_url, '/');
        $basePattern = trim($request->base_pattern, '/');

        UrlAlias::create([
            'custom_url' => $customUrl,
            'base_pattern' => $basePattern,
            'redirect_type' => $request->redirect_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.url-aliases.index')
                        ->with('success', 'URL alias created successfully!');
    }

    public function edit(UrlAlias $urlAlias)
    {
        return view('admin.url-aliases.edit', compact('urlAlias'));
    }

    public function update(Request $request, UrlAlias $urlAlias)
    {
        $request->validate([
            'custom_url' => 'required|string|regex:/^[a-zA-Z0-9_-]+$/|unique:url_aliases,custom_url,' . $urlAlias->id,
            'base_pattern' => 'required|string',
            'redirect_type' => 'required|in:301,302,canonical',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $urlAlias->update([
            'custom_url' => trim($request->custom_url, '/'),
            'base_pattern' => trim($request->base_pattern, '/'),
            'redirect_type' => $request->redirect_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.url-aliases.index')
                        ->with('success', 'URL alias updated successfully!');
    }

    public function destroy(UrlAlias $urlAlias)
    {
        $urlAlias->delete();
        return redirect()->route('admin.url-aliases.index')
                        ->with('success', 'URL alias deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggle(UrlAlias $urlAlias)
    {
        $urlAlias->update(['is_active' => !$urlAlias->is_active]);
        
        $status = $urlAlias->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "URL alias {$status} successfully!");
    }
}
