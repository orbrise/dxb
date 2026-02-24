<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use File;
 
class AppSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();

        return view('admin.appsetting', compact('settings'));
    }

    public function update(Request $req)
    {
        $req->validate([
            'app_name' => 'required|string|max:255',
            'keywords' => 'nullable|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'app_logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'admin_bg' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'collapse_icon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'auto_archive_days' => 'nullable|integer|min:1|max:365',
            'archive_warning_days' => 'nullable|integer|min:1|max:30',
            'auto_delete_archived_days' => 'nullable|integer|min:1|max:365',
            'auto_delete_inactive_days' => 'nullable|integer|min:1|max:365',
        ]);
    
        $set = Setting::find(1);
        $set->app_name = $req->app_name;
    
        if ($req->hasFile('app_logo')) {
            $file = $req->file('app_logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Use external assets path
            $external_assets_path = env('EXTERNAL_ASSETS_PATH', 'C:\assets_storage');
            $uploads_dir = $external_assets_path . '/uploads';
            
            if (!File::isDirectory($uploads_dir)) {
                File::makeDirectory($uploads_dir, 0777, true);
            }
            
            $file->move($uploads_dir, $filename);
            $set->app_logo = 'uploads/' . $filename;
        }
    
        if ($req->hasFile('favicon')) {
            $file = $req->file('favicon');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Use external assets path
            $external_assets_path = env('EXTERNAL_ASSETS_PATH', 'C:\assets_storage');
            $uploads_dir = $external_assets_path . '/uploads';
            
            if (!File::isDirectory($uploads_dir)) {
                File::makeDirectory($uploads_dir, 0777, true);
            }
            
            $file->move($uploads_dir, $filename);
            $set->favicon = 'uploads/' . $filename;
        }
    
        if ($req->hasFile('admin_bg')) {
            $file = $req->file('admin_bg');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Use external assets path
            $external_assets_path = env('EXTERNAL_ASSETS_PATH', 'C:\assets_storage');
            $uploads_dir = $external_assets_path . '/uploads';
            
            if (!File::isDirectory($uploads_dir)) {
                File::makeDirectory($uploads_dir, 0777, true);
            }
            
            $file->move($uploads_dir, $filename);
            $set->admin_bg = 'uploads/' . $filename;
        }

        if ($req->hasFile('collapse_icon')) {
            $file = $req->file('collapse_icon');
            $filename = 'collapse_logo_' . time() . '_' . $file->getClientOriginalName();
            
            // Use external assets path
            $external_assets_path = env('EXTERNAL_ASSETS_PATH', 'C:\assets_storage');
            $uploads_dir = $external_assets_path . '/uploads';
            
            if (!File::isDirectory($uploads_dir)) {
                File::makeDirectory($uploads_dir, 0777, true);
            }
            
            $file->move($uploads_dir, $filename);
            $set->collapse_icon = 'uploads/' . $filename;
        }
    
        $set->title = $req->title;
        $set->keywords = $req->keywords;
        $set->description = $req->description;
        
        // Auto-archive settings
        $set->auto_archive_enabled = $req->has('auto_archive_enabled');
        $set->auto_archive_days = $req->auto_archive_days ?? 30;
        $set->send_archive_warning = $req->has('send_archive_warning');
        $set->archive_warning_days = $req->archive_warning_days ?? 3;
    // Auto-delete settings
    $set->auto_delete_archived_enabled = $req->has('auto_delete_archived_enabled');
    $set->auto_delete_archived_days = $req->auto_delete_archived_days ?? 60;
    $set->auto_delete_inactive_enabled = $req->has('auto_delete_inactive_enabled');
    $set->auto_delete_inactive_days = $req->auto_delete_inactive_days ?? 90;
    
    // Geo redirect setting
    $set->geo_redirect_enabled = $req->has('geo_redirect_enabled');
        
        $set->save();
        
        // Clear geo redirect cache so changes take effect immediately
        Cache::forget('geo_redirect_enabled');
    
        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * Clear all application caches
     */
    public function clearCache()
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            
            // Clear config cache
            Artisan::call('config:clear');
            
            // Clear view cache
            Artisan::call('view:clear');
            
            // Clear route cache
            Artisan::call('route:clear');
            
            // Clear compiled classes
            Artisan::call('clear-compiled');
            
            return back()->with('cache_success', 'All caches have been cleared successfully!');
        } catch (\Exception $e) {
            return back()->with('cache_error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}