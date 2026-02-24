<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogSettingController extends Controller
{
    /**
     * Display the blog settings form
     */
    public function index()
    {
        $seoSettings = BlogSetting::getByGroup('seo');
        $socialSettings = BlogSetting::getByGroup('social');
        $analyticsSettings = BlogSetting::getByGroup('analytics');
        $schemaSettings = BlogSetting::getByGroup('schema');

        return view('admin.blog.settings.index', compact(
            'seoSettings',
            'socialSettings',
            'analyticsSettings',
            'schemaSettings'
        ));
    }

    /**
     * Update the blog settings
     */
    public function update(Request $request)
    {
        $settings = BlogSetting::all();

        foreach ($settings as $setting) {
            $key = $setting->key;
            
            // Handle file uploads
            if ($setting->type === 'image' && $request->hasFile($key)) {
                $file = $request->file($key);
                $filename = 'blog_' . $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('blog/settings', $filename, 'public');
                BlogSetting::set($key, 'storage/' . $path);
            } 
            // Handle boolean fields
            elseif ($setting->type === 'boolean') {
                BlogSetting::set($key, $request->has($key) ? '1' : '0');
            }
            // Handle text fields
            elseif ($request->has($key)) {
                BlogSetting::set($key, $request->input($key));
            }
        }

        // Clear cache
        BlogSetting::clearCache();

        return redirect()->route('admin.blog.settings.index')
            ->with('success', 'Blog SEO settings updated successfully!');
    }
}
