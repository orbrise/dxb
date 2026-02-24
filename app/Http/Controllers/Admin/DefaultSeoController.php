<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DefaultSeoSetting;

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
