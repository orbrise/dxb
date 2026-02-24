<?php

namespace App\Livewire\Profile\Users;
use Livewire\WithFileUploads;
use Livewire\Component;
use App\Models\Listing;
use App\Models\Service;
use App\Models\Country;
use App\Models\User;
use App\Models\ProfileImage;
use App\Models\UserService;
use App\Models\Gender;
use App\Models\Currency;
use App\Models\Ethnicity;
use App\Models\Bust;
use App\Models\HairColor;
use App\Models\Language;
use App\Models\UserLanguage;
use App\Models\UsersProfile;
use Auth;
use File;
use Session;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Storage;
use DB;
use Log;

class UserProfile extends Component
{ use WithFileUploads;
   
    public $username,$id, $query, $data, $name,$listing, $city, $aboutme, $countrycode, $phone, $services = [],$selectedcity, $mphoto, $countrycode2, $phone2,
    $iswhatsapp, $iswechat, $istelegram, $issignal, $iswhatsapp2, $iswechat2, $istelegram2, $issignal2, $website, $onlyfans, $gender, $incall, 
    $outcall, $incallprice, $outcallprice, $incallcurr, $outcallcurr, $ori, $ethnicity, $height, $age, $bust, $haircolor, $nationality, $num, $language1,
    $language2, $language3,$language4,$language5, $expert1,$expert2,$expert3,$expert4,$expert5, $shaved, $video, $smoke, $random;
    public $mainImage = 0;
    public $tempImages = [];
    public $imagePositions = [];
    protected $listeners = ['clearLanguage'];
    protected $rules = [
        'name' => 'required|min:3|max:40',
        'aboutme' => 'required|min:50|max:2000',
        'selectedcity' => 'required',
        'gender' => 'required',
        'mphoto' => 'nullable|array',
        'mphoto.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        'tempImages' => 'array',
        'tempImages.*' => 'image|mimes:jpeg,png,jpg|max:5120'
    ];


    public function mount($username, $id) {
        $this->random = uniqid();
        Session::put('random', $this->random);
        $this->username = $username;
        $this->id = $id;
        $user = UsersProfile::find($id);
        $this->data = [];
        $this->name = $user->name;
        $this->listing = $user->listing;
        if(empty($user->city)){
            $this->selectedcity = "Dubai";
        } else {
            $this->selectedcity = $user->getcity->name;
        }
        $this->city = $user->city;
        
        $this->aboutme = stripcslashes($user->about);
        
        // Auto-select country code based on domain if not already set
        if (empty($user->country_code)) {
            $country = getCountryFromDomain();
            if ($country && $country->phonecode) {
                $this->countrycode = $country->phonecode;
            }
        } else {
            $this->countrycode = $user->country_code;
        }
        
        $this->phone = $user->phone;
        
        // Auto-select second country code based on domain if not already set
        if (empty($user->country_code2)) {
            $country = getCountryFromDomain();
            if ($country && $country->phonecode) {
                $this->countrycode2 = $country->phonecode;
            }
        } else {
            $this->countrycode2 = $user->country_code2;
        }
        
        $this->phone2 = $user->phone2;
        $this->website = $user->website;
        $this->onlyfans = $user->onlyfans;
        $this->iswhatsapp = (bool) $user->iswhatsapp;
        $this->istelegram = (bool) $user->istelegram;
        $this->iswechat = (bool) $user->iswechat;
        $this->issignal = (bool) $user->issignal;

        $this->iswhatsapp2 = (bool) $user->iswhatsapp2;
        $this->istelegram2 = (bool) $user->istelegram2;
        $this->iswechat2 = (bool) $user->iswechat2;
        $this->issignal2 = (bool) $user->issignal2;


        $uservices = UserService::where(["user_id" => Auth::id(), 'profile_id' => $id])
        ->pluck("service_id")
        ->toArray();
    $this->services = array_fill_keys($uservices, true);

   
        $this->incall =  (bool)$user->incall;
        $this->outcall =  (bool)$user->outcall;
        $this->incallprice = $user->incallprice;
        $this->outcallprice = $user->outcallprice;
        if(!empty($user->incallcurr)){
            $this->incallcurr = $user->incallcurr;
        } else {
            $this->incallcurr = "AED";
        }
        
        if(!empty($user->outcallcurr)){
            $this->outcallcurr = $user->outcallcurr;
        } else {
            $this->outcallcurr = "AED";
        }

        $this->gender = $user->gender;
        $this->ori = (bool) $user->orientation;
        $this->ethnicity = $user->ethnicity;
        $this->age = $user->age;
        $this->bust = $user->bust;
        $this->haircolor = $user->haircolor;
        $this->nationality = $user->nationality;
        $this->height = $user->height;

        $userlangs = UserLanguage::where([
            "profile_id" => $id
        ])->get();


        foreach($userlangs as $key => $lang) {
            if($key == 0) {
                $this->language1 = $lang->language_id;
                $this->expert1 = $lang->expert;
            }
    
            if($key == 1) {
                $this->language2 = $lang->language_id;
                $this->expert2 = $lang->expert;
            }

            if($key == 2) {
                $this->language3 = $lang->language_id;
                $this->expert3 = $lang->expert;
            }

        }
        $this->shaved = $user->shaved;
        $this->smoke =  $user->smoke;
        $this->video = $user->video;

        $mainImage = ProfileImage::where([
            'profile_id' => $id,
            'is_main' => true
        ])->first();
        
        $this->mainImage = $mainImage ? $mainImage->id : null;

    }
   
    public function updatedMainImage($value)
{
    ProfileImage::where('profile_id', $this->id)->update(['is_main' => false]);
    ProfileImage::where('id', $value)->update(['is_main' => true]);
}


    public function render()
    {
        $listings = Listing::all();
        $services = Service::all();
        $countries = Country::all();
        $user = Auth::user();
        $genders = Gender::all();
        $pimgs = ProfileImage::where(['user_id' => Auth::user()->id, 'profile_id' => $this->id])
            ->orderBy('image_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $currencies = Currency::select('id', 'code', 'symbol')
            ->get()
            ->unique('code')
            ->sortBy('code')
            ->values(); 
        $ethnicities= Ethnicity::all();
        $busts = Bust::all();
        $hairs = HairColor::all();
        $languages = Language::all();
        $uservices = UserService::where(["user_id" =>  Auth::user()->id, 'profile_id' => $this->id])->pluck("service_id")->toArray();
       $userlangs = UserLanguage::where(["user_id" =>  Auth::user()->id, 'profile_id' => $this->id])->get();
      
        return view('livewire.profile.users.user-profile', compact('listings','services', 'countries', 'user', 'pimgs', 'genders',
        'currencies','ethnicities','busts', 'hairs', 'languages', 'uservices', 'userlangs'));
    }

    public function updateProfile()
{
    $validated = $this->validate([
        'name' => 'required',
        'aboutme' => 'required',
        'selectedcity' => 'required',
        'gender' => 'required',
    ]);

    // Check minimum character count (50 characters)
    $charCount = strlen(trim(strip_tags($this->aboutme)));
    if ($charCount < 50) {
        $this->addError('aboutme', 'Your "About me" description must contain at least 50 characters. Currently: ' . $charCount . ' characters.');
        return;
    }

    DB::beginTransaction();
    try {
        $profile = UsersProfile::findOrFail($this->id);
        
        // Update basic profile information
        $profile->update([
            'name' => $this->name,
            'listing' => $this->listing,
            'city' => $this->city,
            'about' => $this->aboutme,
            'phone' => $this->phone,
            'country_code' => $this->countrycode,
            'phone2' => $this->phone2,
            'country_code2' => $this->countrycode2,
            'website' => $this->website,
            'onlyfans' => $this->onlyfans,
            'gender' => $this->gender,
            'orientation' => $this->ori,
            'ethnicity' => $this->ethnicity,
            'height' => $this->height,
            'nationality' => $this->nationality,
            'age' => $this->age,
            'haircolor' => $this->haircolor,
            'bust' => $this->bust,
            'shaved' => $this->shaved,
            'smoke' => $this->smoke,
            'video' => $this->video,
            'incall' => $this->incall ? true : false,
            'outcall' => $this->outcall ? true : false,
        ]);

        // Update messaging preferences
        $messagingFields = [
            'iswhatsapp', 'istelegram', 'iswechat', 'issignal',
            'iswhatsapp2', 'istelegram2', 'iswechat2', 'issignal2'
        ];

        foreach ($messagingFields as $field) {
            if ($this->$field) {
                $profile->$field = true;
            }
        }

        // Update pricing information
        if ($this->incall) {
            $profile->incallprice = $this->incallprice;
            $profile->incallcurr = $this->incallcurr;
        }

        if ($this->outcall) {
            $profile->outcallprice = $this->outcallprice;
            $profile->outcallcurr = $this->outcallcurr;
        }

        $profile->save();

        // Update services
        UserService::where('profile_id', $this->id)->delete();

        // Then create new services entries
        foreach ($this->services as $serviceId => $isSelected) {
            if ($isSelected) {
                UserService::create([
                    'user_id' => Auth::id(),
                    'service_id' => $serviceId,
                    'profile_id' => $this->id
                ]);
            }
        }

        // Update languages
        UserLanguage::where([
            'user_id' => Auth::id(),
            'profile_id' => $this->id
        ])->delete();

      
        for ($i = 1; $i <= 5; $i++) {
            $languageId = $this->{"language$i"};
            $expertLevel = $this->{"expert$i"};
            
            if ($languageId && $expertLevel) {
                UserLanguage::create([
                    'user_id' => Auth::id(),
                    'profile_id' => $this->id,
                    'language_id' => $languageId,
                    'expert' => $expertLevel
                ]);
            }
        }

        if (!empty($this->tempImages)) {
            // Use external disk for storing user images
            $externalDisk = Storage::disk('assets_external');
            $basePath = "userimages/{$profile->user_id}/{$profile->id}";
            
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

            foreach($this->tempImages as $key => $image) {
                $fileName = time() . rand(100,999);
                
                $manager = new ImageManager(new Driver());
                $img = $manager->read($image->getRealPath());
                
                // Optimize and save images - Reduced dimensions for faster loading
                // Max 900px for good balance between quality and file size (~50-80KB)
                if ($img->width() > $img->height()) {
                    $img->scaleDown(width: 900);
                } else {
                    $img->scaleDown(height: 900);
                }
                
                // Create temporary files
                $tempJpgPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
                $tempWebpPath = sys_get_temp_dir() . '/' . uniqid() . '.webp';
                $tempThumbPath = sys_get_temp_dir() . '/' . uniqid() . '_thumb.webp';
                
                // Save optimized images - Lower quality for smaller file size (~50-80KB)
                // JPG at 50 quality and WebP at 60 quality - good visual quality, small size
                $img->save($tempJpgPath, quality: 50);
                $img->encode(new WebpEncoder(quality: 60))->save($tempWebpPath);
                
                // Create thumbnail for listing pages (300px max, ~15-25KB)
                $thumbImg = $manager->read($image->getRealPath());
                if ($thumbImg->width() > $thumbImg->height()) {
                    $thumbImg->scaleDown(width: 300);
                } else {
                    $thumbImg->scaleDown(height: 300);
                }
                $thumbImg->encode(new WebpEncoder(quality: 55))->save($tempThumbPath);
                
                // Upload to external disk
                $externalDisk->put("{$basePath}/{$fileName}.jpg", file_get_contents($tempJpgPath));
                $externalDisk->put("{$basePath}/{$fileName}.webp", file_get_contents($tempWebpPath));
                $externalDisk->put("{$basePath}/{$fileName}_thumb.webp", file_get_contents($tempThumbPath));
                
                // Clean up temporary files
                if (file_exists($tempJpgPath)) unlink($tempJpgPath);
                if (file_exists($tempWebpPath)) unlink($tempWebpPath);
                if (file_exists($tempThumbPath)) unlink($tempThumbPath);
                    
                ProfileImage::create([
                    'user_id' => $profile->user_id,
                    'profile_id' => $profile->id,
                    'image' => $fileName.'.jpg',
                    'image_webp' => $fileName.'.webp',
                    'is_main' => $key == $this->mainImage || ($this->mainImage === null && $key == 0) // First image is main if no main selected
                ]);
            }
        }

        DB::commit();
        session()->flash('success', 'Profile updated successfully');
        return redirect()->route('user.dashboard', ['name' => $profile->name, 'id' => $profile->id]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Profile update failed', [
            'error' => $e->getMessage(),
            'user_id' => Auth::id(),
            'profile_id' => $this->id
        ]);
        session()->flash('error', 'Failed to update profile. Please try again.');
        return null;
    }
}

public function updatedMphoto()
{
    $this->validate([
        'mphoto.*' => 'image|mimes:jpeg,png,jpg|max:5120'
    ]);

    if (is_array($this->mphoto)) {
        foreach ($this->mphoto as $photo) {
            $this->tempImages[] = $photo;
        }
    } else {
        $this->tempImages[] = $this->mphoto;
    }
    
    $this->mphoto = null;
    $this->dispatch('fileUploaded');
}

public function removeTemporaryImage($index)
{
    unset($this->tempImages[$index]);
    $this->tempImages = array_values($this->tempImages);
}

public function reorderImages($orderedIndexes)
{
    $newOrder = [];
    foreach ($orderedIndexes as $index) {
        if (isset($this->tempImages[$index])) {
            $newOrder[] = $this->tempImages[$index];
        }
    }
    $this->tempImages = $newOrder;
    $this->mainImage = 0; // First image is always main
}

public function reorderExistingImages($orderedIds)
{
    // Update the order column for each image based on new position
    foreach ($orderedIds as $position => $imageId) {
        ProfileImage::where('id', $imageId)
            ->where('profile_id', $this->id)
            ->update(['image_order' => $position]);
    }
    
    // Set first image as main
    if (count($orderedIds) > 0) {
        ProfileImage::where('profile_id', $this->id)->update(['is_main' => false]);
        ProfileImage::where('id', $orderedIds[0])->update(['is_main' => true]);
        $this->mainImage = $orderedIds[0];
    }
}

public function reorderAllImages($imageData)
{
    // imageData format: [{isExisting: true, id: '123'}, {isExisting: false, index: 0}]
    // Store the unified positions
    $this->imagePositions = [];
    
    $existingIds = [];
    $tempIndexes = [];
    
    foreach ($imageData as $position => $image) {
        // Store position for rendering
        $this->imagePositions[] = [
            'type' => $image['isExisting'] ? 'existing' : 'temp',
            'id' => $image['isExisting'] ? $image['id'] : null,
            'index' => !$image['isExisting'] ? $image['index'] : null,
            'position' => $position
        ];
        
        if ($image['isExisting']) {
            $existingIds[] = $image['id'];
        } else {
            $tempIndexes[] = $image['index'];
        }
    }
    
    // Reorder existing images
    foreach ($existingIds as $position => $imageId) {
        ProfileImage::where('id', $imageId)
            ->where('profile_id', $this->id)
            ->update(['image_order' => $position]);
    }
    
    // Reorder temp images
    $newTempOrder = [];
    foreach ($tempIndexes as $index) {
        if (isset($this->tempImages[$index])) {
            $newTempOrder[] = $this->tempImages[$index];
        }
    }
    $this->tempImages = $newTempOrder;
    
    // Set main image based on what's first
    if (count($imageData) > 0) {
        if ($imageData[0]['isExisting']) {
            ProfileImage::where('profile_id', $this->id)->update(['is_main' => false]);
            ProfileImage::where('id', $imageData[0]['id'])->update(['is_main' => true]);
            $this->mainImage = $imageData[0]['id'];
        } else {
            // First image is a temp image
            ProfileImage::where('profile_id', $this->id)->update(['is_main' => false]);
            $this->mainImage = -1; // Indicates temp image is main
        }
    }
}


    public function removeImg($id)
    {
        $image = ProfileImage::findOrFail($id);
        $basePath = "userimages/{$image->user_id}/{$image->profile_id}";
        
        try {
            // Delete both JPG and WebP versions
            Storage::delete([
                "public/{$basePath}/{$image->image}",
                "public/{$basePath}/{$image->image_webp}"
            ]);
            
            $image->delete();
            
            session()->flash('success', 'Image removed successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to remove image');
            Log::error('Image deletion failed', ['error' => $e->getMessage()]);
        }
    }

    private function updateLanguages($profileId)
    {
        UserLanguage::where('profile_id', $profileId)->delete();
        
        $languages = collect([
            ['lang' => $this->language1, 'level' => $this->expert1],
            ['lang' => $this->language2, 'level' => $this->expert2],
            ['lang' => $this->language3, 'level' => $this->expert3],
            ['lang' => $this->language4, 'level' => $this->expert4],
            ['lang' => $this->language5, 'level' => $this->expert5],
        ])->filter(fn($lang) => $lang['lang']);

        $languages->each(fn($lang) => 
            UserLanguage::create([
                'user_id' => Auth::id(),
                'language_id' => $lang['lang'],
                'expert' => $lang['level'],
                'profile_id' => $profileId
            ])
        );
    }


    public function searchCity(){
       return $this->data = ["dd","dd1"];
    }

    public function clearLanguage($id)
{
    $this->{"language$id"} = null;
    $this->{"expert$id"} = null;
}
}
