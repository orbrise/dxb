<?php

namespace App\Http\Controllers;

use App\Models\VerificationPhoto;
use App\Models\UsersProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerifyPhotoController extends Controller
{

    public function index($slug, $id)
{
    $user = UsersProfile::findOrFail($id);
    $photo_code = mt_rand(1000, 9999);
    
    // Update user's photo code
    $user->update(['photo_code' => $photo_code]);
    
    return view('verify-photo', compact('user', 'photo_code'));
}

public function store(Request $request, $slug, $id)
{
    $user = UsersProfile::findOrFail($id);
    
    // Get the photo data from request
    $photoData = $request->input('photoData');
    
    \Log::info('Verification photo upload attempt', [
        'user_id' => $user->user_id,
        'profile_id' => $id,
        'data_length' => strlen($photoData ?? ''),
        'data_start' => substr($photoData ?? '', 0, 50)
    ]);
    
    // Clean and decode base64 data
    $cleanData = preg_replace('#^data:image/\w+;base64,#i', '', $photoData);
    $cleanData = str_replace(' ', '+', $cleanData);
    $image = base64_decode($cleanData);
    
    \Log::info('After decode', ['binary_length' => strlen($image)]);
    
    if (strlen($image) == 0) {
        \Log::error('Empty image data after decode');
        return back()->with('error', 'Failed to process image data');
    }
    
    $filename = time() . '_verify.jpg';
    
    // Use external disk (same as profile images)
    $externalDisk = Storage::disk('assets_external');
    $path = "userimages/{$user->user_id}/verification/";
    
    // Create directory structure on external disk
    if (!$externalDisk->exists("userimages")) {
        $externalDisk->makeDirectory("userimages", 0755, true);
    }

    if (!$externalDisk->exists("userimages/{$user->user_id}")) {
        $externalDisk->makeDirectory("userimages/{$user->user_id}", 0755, true);
    }

    if (!$externalDisk->exists($path)) {
        $externalDisk->makeDirectory($path, 0755, true);
    }
    
    // Upload to external disk
    $result = $externalDisk->put($path . $filename, $image);
    
    \Log::info('File upload result', [
        'success' => $result,
        'path' => $path . $filename,
        'exists' => $externalDisk->exists($path . $filename)
    ]);

    VerificationPhoto::create([
        'user_id' => $user->user_id,
        'profile_id' => $id,
        'photo' => $filename,
        'status' => 'pending'
    ]);

    return redirect('my-profile/'.$user->slug.'/'.$user->id)
        ->with('success', 'Verification photo uploaded successfully!');
}

public function searchProfiles(Request $request)
{
    $query = $request->input('q', '');
    
    $profiles = UsersProfile::where('user_id', auth()->id())
        ->with(['getcity', 'coverimg', 'singleimg'])
        ->when($query, function($q) use ($query) {
            $q->where(function($subQ) use ($query) {
                $subQ->where('name', 'like', '%' . $query . '%')
                     ->orWhere('id', $query);
            });
        })
        ->orderBy('name', 'asc')
        ->get()
        ->map(function($profile) {
            return [
                'id' => $profile->id,
                'name' => $profile->name,
                'slug' => $profile->slug,
                'user_id' => $profile->user_id,
                'city' => $profile->getcity->name ?? null,
                'cover_image' => $profile->coverimg->image ?? null,
                'single_image' => $profile->singleimg->image ?? null,
            ];
        });
    
    return response()->json([
        'profiles' => $profiles
    ]);
}
}
