<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{UsersProfile, Listing, Service, Country, User, ProfileImage, UserService, 
    Gender, Currency, Ethnicity, Bust, HairColor, Language, UserLanguage, City, Package, Orientation};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class ProfileSettingController extends Controller
{
    public function genders()
    {
        $genders = Gender::all();
        return view('admin.profilesetting.genders', compact('genders'));
    }

    public function addGender(Request $req){

        $req->validate([
        'name' => 'required|unique:genders,name',
        ]);

        $city = new Gender;
        $city->name = $req->name;

        if($city->save()){
            return back()->with("success", "Gender added successfully");
        }
    }


    public function updateGender(Request $req){

        $city = Gender::findOrFail($req->rid);
        $city->name = $req->name;

        if($city->save()){
            return "success";
        }
    }

    public function delGender(Request $req)
    {
        if(Gender::where('id',$req->rid)->delete()){
            return 'success';
        }
    }


    public function hairColors()
{
    $hairColors = HairColor::all();
    return view('admin.profilesetting.haircolor', compact('hairColors'));
}

public function addHairColor(Request $req)
{
    $req->validate([
        'name' => 'required|unique:hair_colors,name',
    ]);

    $hairColor = new HairColor;
    $hairColor->name = $req->name;

    if ($hairColor->save()) {
        return back()->with("success", "Hair Color added successfully");
    }
}

public function updateHairColor(Request $req)
{
    $hairColor = HairColor::findOrFail($req->rid);
    $hairColor->name = $req->name;

    if ($hairColor->save()) {
        return "success";
    }
}

public function delHairColor(Request $req)
{
    if (HairColor::where('id', $req->rid)->delete()) {
        return 'success';
    }
}



public function busts()
{
    $busts = Bust::all();
    return view('admin.profilesetting.busts', compact('busts'));
}

public function addBust(Request $req)
{
    $req->validate([
        'name' => 'required|unique:busts,name',
    ]);

    $bust = new Bust;
    $bust->name = $req->name;

    if ($bust->save()) {
        return back()->with("success", "Bust added successfully");
    }
}

public function updateBust(Request $req)
{
    $bust = Bust::findOrFail($req->rid);
    $bust->name = $req->name;

    if ($bust->save()) {
        return "success";
    }
}

public function delBust(Request $req)
{
    if (Bust::where('id', $req->rid)->delete()) {
        return 'success';
    }
}



public function orientations()
    {
        $orientations = Orientation::all();
        return view('admin.profilesetting.orientations', compact('orientations'));
    }

    public function addOrientation(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:orientations,name',
        ]);

        $orientation = new Orientation;
        $orientation->name = $req->name;

        if ($orientation->save()) {
            return back()->with("success", "Orientation added successfully");
        }
    }

    public function updateOrientation(Request $req)
    {
        $orientation = Orientation::findOrFail($req->rid);
        $orientation->name = $req->name;

        if ($orientation->save()) {
            return "success";
        }
    }

    public function delOrientation(Request $req)
    {
        if (Orientation::where('id', $req->rid)->delete()) {
            return 'success';
        }
    }


 public function ethnicities()
    {
        $ethnicities = Ethnicity::all();
        return view('admin.profilesetting.ethnicities', compact('ethnicities'));
    }

    public function addEthnicity(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:ethnicities,name',
        ]);

        $ethnicity = new Ethnicity;
        $ethnicity->name = $req->name;

        if ($ethnicity->save()) {
            return back()->with("success", "Ethnicity added successfully");
        }
    }

    public function updateEthnicity(Request $req)
    {
        $ethnicity = Ethnicity::findOrFail($req->rid);
        $ethnicity->name = $req->name;

        if ($ethnicity->save()) {
            return "success";
        }
    }

    public function delEthnicity(Request $req)
    {
        if (Ethnicity::where('id', $req->rid)->delete()) {
            return 'success';
        }
    }


     public function languages()
    {
        $languages = Language::all();
        return view('admin.profilesetting.languages', compact('languages'));
    }

    public function addLanguage(Request $req)
    {
        $req->validate([
            'name' => 'required|unique:languages,name',
        ]);

        $language = new Language;
        $language->name = $req->name;

        if ($language->save()) {
            return back()->with("success", "Language added successfully");
        }
    }

    public function updateLanguage(Request $req)
    {
        $language = Language::findOrFail($req->rid);
        $language->name = $req->name;

        if ($language->save()) {
            return "success";
        }
    }

    public function delLanguage(Request $req)
    {
        if (Language::where('id', $req->rid)->delete()) {
            return 'success';
        }
    }


    public function getsettingview()
    {
        return view('admin.profilesetting.profile-setting');
    }

    public function accupdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed'
        ]);

        $user = auth()->user();
        
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect');
            }
            
            $user->password = Hash::make($request->new_password);
        }
        
        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }

    public function index(Request $request)
{
    $query = UsersProfile::with([
        'user', 
        'glisting',
        'languages',
        'services',
        'multipleimgs',
        'getpackage',
        'activeAuction'
    ]); 

    // Apply filters
    if ($request->id) {
        $query->where('id', $request->id);
    }
    if ($request->city) {
        $query->where('city', $request->city);
    }
    if ($request->title) {
        $query->where('name', 'like', '%' . $request->title . '%');
    }
    if ($request->start_date && $request->end_date) {
        $query->whereBetween('created_at', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59'
        ]);
    }
    if ($request->status || $request->status === '0') {
        if ($request->status === 'archived') {
            $query->archived();
        } else {
            $query->where('is_active', $request->status == 1 ? 1 : 0);
            // When filtering by active/inactive status, only show non-archived profiles
            $query->active();
        }
    } else {
        // By default, show only active (non-archived) profiles
        $query->active();
    }
    
    if ($request->premium) {
        if ($request->premium === 'auction') {
            $query->whereHas('activeAuction');
        } else {
            // Get the package name from the selected ID
            $selectedPackage = Package::find($request->premium);
            if ($selectedPackage) {
                // Match all packages with the same name (handles duplicates in DB)
                $packageIds = Package::where('name', $selectedPackage->name)->pluck('id')->toArray();
                $query->whereIn('package_id', $packageIds);
            } else {
                $query->where('package_id', $request->premium);
            }
        }
    }

    $profiles = $query->latest()->paginate($request->get('per_page', 10));
    $profiles->appends($request->query());
    $cities = City::all();
    // Get unique packages by name to avoid duplicates in filter dropdown
    $packages = Package::select('id', 'name')
        ->orderBy('name')
        ->get()
        ->unique('name')
        ->values();

    if ($request->ajax()) {
        $tableHtml = view('admin.profiles.table', compact('profiles'))->render();
        
        // Generate entries info
        $entriesInfo = '';
        if ($profiles->total() > 0) {
            $entriesInfo = 'Showing ' . number_format($profiles->firstItem()) . ' to ' . number_format($profiles->lastItem()) . ' of ' . number_format($profiles->total()) . ' entries';
        } else {
            $entriesInfo = 'Showing 0 to 0 of 0 entries';
        }
        
        return response()->json([
            'table' => $tableHtml,
            'entriesInfo' => $entriesInfo
        ]);
    }

    return view('admin.profiles.index', compact('profiles', 'cities', 'packages'));
}

    public function edit($id)
    {
        $profile = UsersProfile::with([
            'user',
            'languages',
            'services',
            'allImages'
        ])->findOrFail($id);

        // Get country from profile's city
        $country = null;
        if ($profile->city) {
            $city = City::find($profile->city);
            if ($city && $city->country) {
                $country = Country::where('nicename', $city->country)->first();
            }
        }

        // Get packages based on profile's country
        $packages = collect();
        if ($country) {
            // Get country-specific packages
            $countryPackages = Package::where('is_global', false)
                ->whereHas('countryPrices', function($query) use ($country) {
                    $query->where('country_id', $country->id);
                })
                ->get();
            
            // If country has specific packages, use them. Otherwise, use global packages
            if ($countryPackages->count() > 0) {
                $packages = $countryPackages;
            } else {
                // Show global packages only when no country-specific packages exist
                $packages = Package::where('is_global', true)->get();
            }
        } else {
            // If no country found, show only global packages
            $packages = Package::where('is_global', true)->get();
        }

        $data = [
            'profile' => $profile,
            'listings' => Listing::all(),
            'services' => Service::all(),
            'countries' => Country::all(),
            'genders' => Gender::all(),
            'currencies' => Currency::all(),
            'ethnicities' => Ethnicity::all(),
            'busts' => Bust::all(),
            'hairs' => HairColor::all(),
            'languages' => Language::all(),
            'cities' => City::all(),
            'packages' => $packages,
        ];
        
        return view('admin.profiles.edit', $data);
    }

    public function update(Request $request, $id)
{
    
    $validated = $request->validate([
        'name' => 'required',
        'listing' => 'required',
        'city' => 'required',
        'about' => 'required',
        'country_code' => 'required_with:phone',
        'phone' => 'required_with:country_code',
        'age' => 'required|numeric|between:18,60',
        'height' => 'nullable|numeric'
    ]);

    $packageid = '';
    $featured = 0;
    $profile = UsersProfile::findOrFail($id);
    if($request->has('package') && $request->package){
        $packageid = $request->package;
        $featured = 1;
    }
    
    // Handle package tier/duration changes
    $packageDays = $profile->package_days; // Keep existing
    $packageExpires = $profile->package_expires_at; // Keep existing
    
    if($request->filled('package_tier')) {
        $packageDays = (int) $request->package_tier;
        // Set new expiry date from today + days
        $packageExpires = now()->addDays($packageDays);
    }
    
    $profile->update([
        'name' => $request->name,
        'listing' => $request->listing,
        'city' => $request->city,
        'about' => $request->about,
        'country_code' => $request->country_code,
        'phone' => $request->phone,
        'iswhatsapp' => $request->has('iswhatsapp'),
        'istelegram' => $request->has('istelegram'),
        'iswechat' => $request->has('iswechat'),
        'issignal' => $request->has('issignal'),
        'country_code2' => $request->country_code2,
        'phone2' => $request->phone2,
        'iswhatsapp2' => $request->has('iswhatsapp2'),
        'istelegram2' => $request->has('istelegram2'),
        'iswechat2' => $request->has('iswechat2'),
        'issignal2' => $request->has('issignal2'),
        'website' => $request->website,
        'onlyfans' => $request->onlyfans,
        'gender' => $request->gender,
        'incall' => $request->has('incall'),
        'outcall' => $request->has('outcall'),
        'orientation' => $request->orientation,
        'height' => $request->height,
        'haircolor' => $request->haircolor,
        'nationality' => $request->nationality,
        'bust' => $request->bust,
        'age' => $request->age,
        'incallprice' => $request->incallprice,
        'outcallprice' => $request->outcallprice,
        'ethnicity' => $request->ethnicity,
        'incallcurr' => $request->incallcurr,
        'outcallcurr' => $request->outcallcurr,
        'shaved' => $request->shaved,
        'smoke' => $request->has('smoke'),
        'video' => $request->video,
        'tweets' => $request->tweets,
        'is_active' => $request->has('is_active'),
        'is_featured' => $request->has('is_featured'),
        'package_id' => $request->package_id,
        'slug' => Str::slug($request->name),
        'is_featured' => $featured,
        'package_id' => $packageid,
        'package_days' => $packageDays,
        'package_expires_at' => $packageExpires,
    ]);
     
    // Update created_at when package tier is changed/renewed so profile shows on top in listings
    if($request->filled('package_tier') && $packageid) {
        $profile->created_at = now();
        $profile->save();
    }

    
    // Handle languages
    UserLanguage::where('profile_id', $id)->delete();
    
    for($i = 1; $i <= 5; $i++) {
        if($request->input("language{$i}")) {
       
            UserLanguage::create([
                'user_id' => $profile->user_id,
                'language_id' => $request->input("language{$i}"),
                'expert' => $request->input("expert{$i}"),
                'profile_id' => $id
            ]);
        }
    }


    
    UserService::where('profile_id', $id)->delete();
    if($request->services) {
        foreach($request->services as $service) {
            UserService::create([
                'user_id' => $profile->user_id,
                'service_id' => $service,
                'profile_id' => $id
            ]);
        }
    }

    return redirect()->route('admin.profiles.index')
        ->with('success', 'Profile updated successfully');
}

    public function destroy($id)
    {
        $profile = UsersProfile::findOrFail($id);
        $profile->delete();
        return redirect()->route('admin.profiles.index')
            ->with('success', 'Profile deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'profile_ids' => 'required|array',
            'profile_ids.*' => 'exists:users_profiles,id'
        ]);

        $deletedCount = UsersProfile::whereIn('id', $request->profile_ids)->delete();
        
        return redirect()->route('admin.profiles.index')
            ->with('success', "{$deletedCount} profile(s) deleted successfully");
    }

    /**
     * Archive a profile
     */
    public function archive(Request $request, $id)
    {
        $profile = UsersProfile::findOrFail($id);
        
        $reason = $request->input('reason', 'Archived by admin');
        $profile->archive($reason);

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile archived successfully',
                'profile_id' => $id
            ]);
        }

        return redirect()->back()->with('success', 'Profile archived successfully');
    }

    /**
     * Repost (unarchive) a profile
     */
    public function repost($id)
    {
        $profile = UsersProfile::findOrFail($id);
        $profile->repost();

        return response()->json([
            'success' => true,
            'message' => 'Profile reposted successfully',
            'profile_id' => $id
        ]);
    }

    /**
     * Bulk archive profiles
     */
    public function bulkArchive(Request $request)
    {
        $request->validate([
            'profile_ids' => 'required|array',
            'profile_ids.*' => 'exists:users_profiles,id',
            'reason' => 'nullable|string|max:255'
        ]);

        $reason = $request->input('reason', 'Bulk archived by admin');
        $archivedCount = 0;

        foreach ($request->profile_ids as $id) {
            $profile = UsersProfile::find($id);
            if ($profile && !$profile->isArchived()) {
                $profile->archive($reason);
                $archivedCount++;
            }
        }
        
        return redirect()->route('admin.profiles.index')
            ->with('success', "{$archivedCount} profile(s) archived successfully");
    }

    /**
     * Bulk repost profiles
     */
    public function bulkRepost(Request $request)
    {
        $request->validate([
            'profile_ids' => 'required|array',
            'profile_ids.*' => 'exists:users_profiles,id'
        ]);

        $repostedCount = 0;

        foreach ($request->profile_ids as $id) {
            $profile = UsersProfile::find($id);
            if ($profile && $profile->isArchived()) {
                $profile->repost();
                $repostedCount++;
            }
        }
        
        return redirect()->route('admin.profiles.index')
            ->with('success', "{$repostedCount} profile(s) reposted successfully");
    }

    public function getPackage($id)
    {
        $package = Package::with('countryPrices')->find($id);
        
        if (!$package) {
            return response()->json(['error' => 'Package not found'], 404);
        }
        
        return response()->json($package);
    }

    /**
     * Upload images for a profile
     */
    public function uploadImages(Request $request, $id)
    {
        $profile = UsersProfile::findOrFail($id);
        
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $uploadedImages = [];
        $basePath = "userimages/{$profile->user_id}/{$profile->id}";
        
        // Use external disk for storing user images (same as user profile)
        $externalDisk = Storage::disk('assets_external');
        
        // Create directory structure on external disk
        if (!$externalDisk->exists("userimages")) {
            $externalDisk->makeDirectory("userimages", 0755, true);
        }
        if (!$externalDisk->exists("userimages/{$profile->user_id}")) {
            $externalDisk->makeDirectory("userimages/{$profile->user_id}", 0755, true);
        }
        if (!$externalDisk->exists($basePath)) {
            $externalDisk->makeDirectory($basePath, 0755, true);
        }
        
        // Get current max order
        $maxOrder = ProfileImage::where('profile_id', $id)->max('image_order') ?? -1;
        
        foreach ($request->file('images') as $image) {
            $maxOrder++;
            
            // Generate unique filename
            $fileName = time() . rand(100, 999);
            $jpgFilename = $fileName . '.jpg';
            $webpFilename = $fileName . '.webp';
            $savedWebp = false;
            
            try {
                // Use Intervention Image v3 (same as UserProfile)
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
                $img = $manager->read($image->getRealPath());
                
                // Optimize and resize (max 1200px)
                if ($img->width() > $img->height()) {
                    $img->scaleDown(width: 1200);
                } else {
                    $img->scaleDown(height: 1200);
                }
                
                // Create temporary files
                $tempJpgPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
                $tempWebpPath = sys_get_temp_dir() . '/' . uniqid() . '.webp';
                
                // Save optimized JPG
                $img->save($tempJpgPath, quality: 65);
                $externalDisk->put("{$basePath}/{$jpgFilename}", file_get_contents($tempJpgPath));
                @unlink($tempJpgPath);
                
                // Try to save WebP
                try {
                    $img->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 75))->save($tempWebpPath);
                    $externalDisk->put("{$basePath}/{$webpFilename}", file_get_contents($tempWebpPath));
                    @unlink($tempWebpPath);
                    $savedWebp = true;
                } catch (\Exception $webpError) {
                    \Log::warning('WebP encoding failed: ' . $webpError->getMessage());
                    $webpFilename = $jpgFilename; // Use JPG as fallback
                }
                
            } catch (\Exception $e) {
                \Log::warning('Image processing failed: ' . $e->getMessage());
                // Fallback: just upload original as JPG
                $externalDisk->put("{$basePath}/{$jpgFilename}", file_get_contents($image->getRealPath()));
                $webpFilename = $jpgFilename;
            }
            
            // Create database record
            $profileImage = ProfileImage::create([
                'user_id' => $profile->user_id,
                'profile_id' => $id,
                'image' => $jpgFilename,
                'image_webp' => $webpFilename,
                'image_order' => $maxOrder,
                'is_main' => $maxOrder === 0
            ]);
            
            // Return JPG URL for immediate display (more compatible)
            $uploadedImages[] = [
                'id' => $profileImage->id,
                'url' => smart_asset("{$basePath}/{$jpgFilename}"),
                'order' => $maxOrder
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'images' => $uploadedImages
        ]);
    }

    /**
     * Delete a profile image
     */
    public function deleteImage($imageId)
    {
        $image = ProfileImage::findOrFail($imageId);
        $basePath = "userimages/{$image->user_id}/{$image->profile_id}";
        
        try {
            // Delete files from external disk
            $externalDisk = Storage::disk('assets_external');
            $externalDisk->delete("{$basePath}/{$image->image}");
            $externalDisk->delete("{$basePath}/{$image->image_webp}");
            
            // Delete database record
            $image->delete();
            
            // Reorder remaining images
            $remainingImages = ProfileImage::where('profile_id', $image->profile_id)
                ->orderBy('image_order')
                ->get();
            
            foreach ($remainingImages as $index => $img) {
                $img->update([
                    'image_order' => $index,
                    'is_main' => $index === 0
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
            ], 500);
        }
    }

    /**
     * Reorder profile images
     */
    public function reorderImages(Request $request, $id)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer'
        ]);

        $profile = UsersProfile::findOrFail($id);
        
        foreach ($request->order as $position => $imageId) {
            ProfileImage::where('id', $imageId)
                ->where('profile_id', $id)
                ->update([
                    'image_order' => $position,
                    'is_main' => $position === 0
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully'
        ]);
    }

}




