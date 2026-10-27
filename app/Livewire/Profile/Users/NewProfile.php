<?php
namespace App\Livewire\Profile\Users;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\{Listing, Service, Country, City, User, ProfileImage, ProfileVideo, UserService,
    Gender, Currency, Ethnicity, Bust, HairColor, Language, UserLanguage, UsersProfile};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Storage;

class NewProfile extends Component
{
    use WithFileUploads;

    public function __construct()
    {
        if (!auth()->check()) {
            redirect()->route('sign-in')->send();
        }

      
        
    }


    public $citySearchResults = [];
    // Basic Info
    public $name;
    public $listing;
    public $city;
    public $selectedcity;
    public $aboutme;
    
    // Contact Info
    public $countrycode;
    public $phone;
    public $countrycode2;
    public $phone2;
    public $showSecondPhone  = false;
    // Social/Messaging
    public $iswhatsapp = false;
    public $iswechat = false;
    public $istelegram = false;
    public $issignal = false;
    public $iswhatsapp2 = false;
    public $iswechat2 = false;
    public $istelegram2 = false;
    public $issignal2 = false;
    public $website;
    public $onlyfans;
    
    // Profile Details
    public $gender = '1';
    public $incall = false;
    public $outcall = false;
    public $incallprice;
    public $outcallprice;
    public $incallcurr = 'AED';
    public $outcallcurr = 'AED';
    public $ori;
    public $ethnicity;
    public $height;
    public $age;
    public $bust;
    public $haircolor;
    public $nationality;
    
    // Languages
    public $language1;
    public $language2;
    public $language3;
    public $language4;
    public $language5;
    public $expert1;
    public $expert2;
    public $expert3;
    public $expert4;
    public $expert5;
    
    // Additional Features
    public $shaved;
    public $video;
    public $smoke = false;
    public $services = [];
    public $mphoto;
    public $num;
    public $random;

    public $mainImage = 0;
    public $tempImages = [];

    /**
     * Video uploads — mirrors $mphoto / $tempImages. Cap of 10 videos per
     * profile enforced in updatedMvideo() + addUploadedTempVideo(). Each
     * tempVideo gets a client-captured first-frame thumbnail in the
     * $videoThumbnails array (indexed-matched), stored as base64 JPEG data
     * URL so it survives Livewire request serialization — we decode it to
     * a real JPEG on save (saveProfileWithPackage) and store beside the MP4.
     */
    public $mvideo;
    public $tempVideos = [];
    public $videoThumbnails = []; // base64 data URLs keyed by tempVideos index

    protected $rules = [
        'name' => 'required|min:3|max:40',
        'city' => 'required', // Add this rule
        'selectedcity' => 'required',
        'aboutme' => 'required|min:50|max:2000', // Description must be 50-2000 characters
        'gender' => 'required',
        'phone' => 'required_with:countrycode',
        'countrycode' => 'required_with:phone',
        'age' => 'required|numeric|between:18,60',
        'height' => 'nullable|numeric|between:140,200', // Height must be 140-200
        'mphoto' => 'nullable|array',
        'mphoto.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        'tempImages' => 'array',
        'tempImages.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        // 50 MB per video, mp4/webm/mov accepted. Count-cap (10) enforced
        // in updatedMvideo() so we can show a specific friendly error.
        'mvideo' => 'nullable|array',
        'mvideo.*' => 'file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
        'tempVideos' => 'array|max:10',
        'tempVideos.*' => 'file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
    ];

    protected $messages = [
        'name.required' => 'Profile name is required',
        'selectedcity.required' => 'Please select a city',
        'aboutme.required' => 'Please provide a description',
        'aboutme.min' => 'Description must be at least 50 characters',
        'gender.required' => 'Please select your gender',
        'age.required' => 'Age is required',
        'age.between' => 'Age must be between 18 and 60',
    ];
    
    public function mount()
    {
        $this->random = uniqid();
        Session::put('random', $this->random);

        if(request()->route()->getName() !== 'new.profile') {
            $profile = UsersProfile::where('user_id', auth()->id())->first();
            if($profile) {
                return redirect()->route('user.dashboard');
            }
        }
        
        // Default to the "escorts" category so the profile is created under
        // the primary /{gender}-escorts-in-{city} URL scheme unless the user
        // explicitly picks a different category.
        $default = Listing::where('is_escort_category', true)->first()
            ?? Listing::orderBy('sort_order')->first();
        if ($default) {
            $this->listing = $default->id;
        }
        
        // Pre-populate phone number from user's account
        $user = auth()->user();
        if ($user) {
            if ($user->country_code) {
                $this->countrycode = $user->country_code;
            }
            if ($user->phone) {
                $this->phone = $user->phone;
            }
        }
    }

    public function updatedMphoto()
{
    // Unified picker: `#mphoto` now accepts both images AND videos. We route
    // each file by its sniffed mime type so the user can drop a mix of
    // photos and clips through one button. Images go to tempImages (8 MB cap,
    // image mimes); videos go to tempVideos (50 MB cap, video mimes with the
    // 10-count limit enforced in the same way as updatedMvideo).
    $incoming = is_array($this->mphoto) ? $this->mphoto : [$this->mphoto];

    foreach ($incoming as $file) {
        if (!$file) continue;
        $mime = $file->getMimeType() ?: '';
        $isVideo = str_starts_with($mime, 'video/');

        try {
            if ($isVideo) {
                // Validate as a video and enforce the 10-cap.
                if (count($this->tempVideos) >= 10) {
                    $this->addError('mphoto', 'You can upload a maximum of 10 videos per profile.');
                    continue;
                }
                validator(
                    ['file' => $file],
                    ['file' => 'file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200']
                )->validate();
                $this->tempVideos[] = $file;
            } else {
                // Validate as an image.
                validator(
                    ['file' => $file],
                    ['file' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192']
                )->validate();
                $this->tempImages[] = $file;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('mphoto', $file->getClientOriginalName() . ': ' . ($e->validator->errors()->first() ?? 'rejected'));
        }
    }

    $this->mphoto = null;
    $this->dispatch('fileUploaded');
}

/**
 * Mirror of updatedMphoto() for videos. Validates mime + size, then
 * appends to $tempVideos. Hard-caps at 10 per profile with a user-visible
 * error so they don't silently lose the 11th-and-on uploads.
 */
public function updatedMvideo()
{
    $this->validate([
        'mvideo.*' => 'file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
    ]);

    $incoming = is_array($this->mvideo) ? $this->mvideo : [$this->mvideo];
    foreach ($incoming as $vid) {
        if (count($this->tempVideos) >= 10) {
            $this->addError('mvideo', 'You can upload a maximum of 10 videos per profile.');
            break;
        }
        $this->tempVideos[] = $vid;
    }

    $this->mvideo = null;
    $this->dispatch('fileUploaded');
}

/**
 * Called by the client-side thumbnail capture JS once the <canvas>
 * finishes drawing the first frame of each video. Payload is a base64
 * JPEG data URL ("data:image/jpeg;base64,...").
 *
 * We decode and write to disk IMMEDIATELY (storage/app/livewire-tmp/vt_*.jpg),
 * then only keep the small temp FILENAME in Livewire state. Prior version
 * kept the full base64 blob in the component property → serialized into
 * every subsequent Livewire payload AND into session on form submit, so a
 * profile with 10 clips carried ~5 MB of base64 on every request. That
 * triggered Cloudflare 520s on the submit (nginx body-size + PHP memory
 * limits) and bloated Redis/file sessions. Keeping only the filename makes
 * each video "cost" ~40 bytes in state regardless of thumbnail size.
 *
 * Guarded: index must exist in tempVideos (prevents stale events from a
 * removed video overwriting an unrelated slot after re-indexing).
 */
#[On('setVideoThumbnail')]
public function setVideoThumbnail($index, $dataUrl)
{
    $i = (int) $index;
    if (!array_key_exists($i, $this->tempVideos)) return;
    if (!is_string($dataUrl) || !str_starts_with($dataUrl, 'data:image/')) return;
    // Reject thumbnails > 500 KB — a 1280x720 JPEG is ~80 KB; anything
    // larger is likely a bug or malicious payload stuffed into the field.
    if (strlen($dataUrl) > 500 * 1024) return;

    if (!preg_match('#^data:image/\w+;base64,(.+)$#', $dataUrl, $m)) return;
    $bytes = base64_decode($m[1], true);
    if ($bytes === false || strlen($bytes) === 0) return;

    // Remove any previous temp thumbnail for this slot so we don't orphan
    // files on disk if the user swaps a video out and in.
    $this->deleteTempThumbnail($this->videoThumbnails[$i] ?? null);

    $filename = 'vt_' . bin2hex(random_bytes(16)) . '.jpg';
    Storage::disk('local')->put('livewire-tmp/' . $filename, $bytes);
    $this->videoThumbnails[$i] = $filename;
}

/** Delete a livewire-tmp thumbnail file by basename. Safe no-op on null / missing. */
private function deleteTempThumbnail(?string $filename): void
{
    if (!$filename) return;
    $safe = basename($filename);
    if ($safe !== $filename) return;
    try { Storage::disk('local')->delete('livewire-tmp/' . $safe); } catch (\Throwable $e) {}
}

public function removeTemporaryVideo($index)
{
    // Clean up the on-disk thumbnail for this slot.
    $this->deleteTempThumbnail($this->videoThumbnails[$index] ?? null);

    unset($this->tempVideos[$index]);
    unset($this->videoThumbnails[$index]);
    // Re-index both arrays so they stay in lockstep (same numeric keys).
    $this->tempVideos      = array_values($this->tempVideos);
    $this->videoThumbnails = array_values($this->videoThumbnails);
}



/**
 * Adds an image to $tempImages from a filename that was already uploaded to
 * storage/app/livewire-tmp/<filename> by the AjaxController fallback route.
 * Reachable two ways: direct call (component.call/$wire) and a global event
 * via Livewire.dispatch('addUploadedTempFile', { filename: '...' }) - the
 * dispatch path is what actually works on this Livewire build because the
 * component instance does not expose .call() / $call() to JS.
 */
#[On('addUploadedTempFile')]
public function addUploadedTempFile($filename)
{
    if (!is_string($filename) || $filename === '') {
        return;
    }

    // Defense in depth: only accept basenames, no path traversal.
    $safe = basename($filename);
    if ($safe !== $filename) {
        return;
    }

    $tempFile = TemporaryUploadedFile::createFromLivewire($safe);
    $this->tempImages[] = $tempFile;
    $this->dispatch('fileUploaded');
}

public function removeTemporaryImage($index)
{
    unset($this->tempImages[$index]);
    $this->tempImages = array_values($this->tempImages);
    
    // Reset main image to 0 if needed
    if ($this->mainImage >= count($this->tempImages)) {
        $this->mainImage = 0;
    }
}

#[On('reorderImages')]
public function reorderImages($orderedIndexes)
{
    $newOrder = [];
    foreach ($orderedIndexes as $index) {
        if (isset($this->tempImages[$index])) {
            $newOrder[] = $this->tempImages[$index];
        }
    }
    $this->tempImages = $newOrder;

    // First image is always the main image
    $this->mainImage = 0;
}

public function setAsMain($index)
{
    if (!isset($this->tempImages[$index])) {
        return;
    }
    $picked = $this->tempImages[$index];
    unset($this->tempImages[$index]);
    array_unshift($this->tempImages, $picked);
    $this->tempImages = array_values($this->tempImages);
    $this->mainImage = 0;
}

public function updatedTempImages()
{
    $this->validate([
        'tempImages.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:8192'
    ]);
}
public function updateProfile()
{
    try {
        $validated = $this->validate();
    } catch (\Illuminate\Validation\ValidationException $e) {
        $this->dispatch('validation-failed');
        throw $e;
    }

    // Custom validation for minimum 50 characters
    $charCount = strlen(trim(strip_tags($this->aboutme)));
    if ($charCount < 50) {
        $this->addError('aboutme', 'Description must contain at least 50 characters. Currently: ' . $charCount . ' characters.');
        $this->dispatch('validation-failed');
        return;
    }

    if (empty($this->tempImages)) {
        $this->addError('mphoto', 'At least one image is required');
        $this->dispatch('validation-failed');
        return;
    }

    // Capture IP and country immediately when form is submitted
    $userIp = request()->ip();
    $userCountry = $this->getCountryFromIp($userIp);
    
    // Debug logging
    \Log::info('Profile Creation - IP Capture', [
        'user_id' => auth()->id(),
        'ip_address' => $userIp,
        'ip_country' => $userCountry,
    ]);
    
    // Store all form data in session for later use
    $sessionData = [
        'name' => $this->name,
        'listing' => $this->listing,
        'city' => $this->city,
        'aboutme' => $this->aboutme,
        'countrycode' => $this->countrycode,
        'countrycode2' => $this->countrycode2,
        'phone' => str_replace(' ', '', $this->phone),
        'phone2' => str_replace(' ', '', $this->phone2),
        'iswhatsapp' => $this->iswhatsapp,
        'istelegram' => $this->istelegram,
        'iswechat' => $this->iswechat,
        'issignal' => $this->issignal,
        'iswhatsapp2' => $this->iswhatsapp2,
        'istelegram2' => $this->istelegram2,
        'iswechat2' => $this->iswechat2,
        'issignal2' => $this->issignal2,
        'website' => $this->website,
        'onlyfans' => $this->onlyfans,
        'services' => $this->services,
        'gender' => $this->gender,
        'ori' => $this->ori,
        'ethnicity' => $this->ethnicity,
        'height' => $this->height,
        'age' => $this->age,
        'bust' => $this->bust,
        'haircolor' => $this->haircolor,
        'nationality' => $this->nationality,
        'language1' => $this->language1,
        'expert1' => $this->expert1,
        'language2' => $this->language2,
        'expert2' => $this->expert2,
        'language3' => $this->language3,
        'expert3' => $this->expert3,
        'language4' => $this->language4,
        'expert4' => $this->expert4,
        'language5' => $this->language5,
        'expert5' => $this->expert5,
        'incall' => $this->incall,
        'outcall' => $this->outcall,
        'incallprice' => $this->incallprice,
        'outcallprice' => $this->outcallprice,
        'incallcurr' => $this->incallcurr,
        'outcallcurr' => $this->outcallcurr,
        'shaved' => $this->shaved,
        'smoke' => $this->smoke,
        'video' => $this->video,
        'mainImage' => $this->mainImage,
        'ip_address' => $userIp,
        'ip_country' => $userCountry,
    ];

    // Store temporary images in session (keep them in Livewire temporary storage)
    $imagePaths = [];
    foreach ($this->tempImages as $key => $image) {
        // Store the temporary path which Livewire will maintain
        $imagePaths[$key] = $image->getRealPath();
    }
    $sessionData['tempImagePaths'] = $imagePaths;

    // Same for videos — real paths + original filenames + mime + size, plus
    // the parallel array of client-captured thumbnails (base64 data URLs).
    // saveProfileWithPackage() reads this to persist both the MP4 and the
    // decoded JPEG beside it in /uservideos/{user_id}/{profile_id}/.
    $videoPaths = [];
    foreach ($this->tempVideos as $key => $vid) {
        $videoPaths[$key] = [
            'path'      => $vid->getRealPath(),
            'original'  => $vid->getClientOriginalName(),
            'mime'      => $vid->getMimeType() ?: 'video/mp4',
            'size'      => $vid->getSize(),
        ];
    }
    $sessionData['tempVideoPaths']  = $videoPaths;
    $sessionData['videoThumbnails'] = $this->videoThumbnails;

    // Store in session with user-specific key
    session(['new_profile_data_' . auth()->id() => $sessionData]);
    
    // Explicitly save session before redirect
    session()->save();
    
    \Log::info('NewProfile: Session data saved', [
        'user_id' => auth()->id(),
        'session_key' => 'new_profile_data_' . auth()->id(),
        'has_data' => !empty($sessionData)
    ]);

    // Redirect to package selection with new profile flag
    return $this->redirect(route('upgrade.profile.new'), navigate: false);
}

public function saveProfileWithPackage($packageId, $duration)
{
    // Retrieve session data
    $sessionData = session('new_profile_data_' . auth()->id());
    
    if (!$sessionData) {
        session()->flash('error', 'Session data expired. Please fill the form again.');
        return redirect()->route('new.profile');
    }

    DB::beginTransaction();

    try {
        // Create profile with package_id
        $profile = UsersProfile::create([
            'user_id' => Auth::id(),
            'name' => $sessionData['name'],
            'slug' => Str::slug($sessionData['name']),
            'listing' => $sessionData['listing'],
            'city' => $sessionData['city'],
            'about' => $sessionData['aboutme'],
            'phone' => $sessionData['phone'],
            'country_code' => $sessionData['countrycode'],
            'iswhatsapp' => $sessionData['iswhatsapp'],
            'istelegram' => $sessionData['istelegram'],
            'iswechat' => $sessionData['iswechat'],
            'issignal' => $sessionData['issignal'],
            'phone2' => $sessionData['phone2'],
            'country_code2' => $sessionData['countrycode2'] ?? null,
            'iswhatsapp2' => $sessionData['iswhatsapp2'],
            'istelegram2' => $sessionData['istelegram2'],
            'iswechat2' => $sessionData['iswechat2'],
            'issignal2' => $sessionData['issignal2'],
            'website' => $sessionData['website'],
            'onlyfans' => $sessionData['onlyfans'],
            'gender' => $sessionData['gender'],
            'incall' => $sessionData['incall'] ? true : false,
            'outcall' => $sessionData['outcall'] ? true : false,
            'incallprice' => $sessionData['incallprice'],
            'incallcurr' => $sessionData['incallcurr'],
            'outcallprice' => $sessionData['outcallprice'],
            'outcallcurr' => $sessionData['outcallcurr'],
            'orientation' => $sessionData['ori'],
            'ethnicity' => $sessionData['ethnicity'],
            'height' => $sessionData['height'],
            'nationality' => $sessionData['nationality'],
            'age' => $sessionData['age'],
            'haircolor' => $sessionData['haircolor'],
            'bust' => $sessionData['bust'],
            'shaved' => $sessionData['shaved'],
            'smoke' => $sessionData['smoke'],
            'video' => $sessionData['video'],
            'ip_address' => $sessionData['ip_address'] ?? null,
            'ip_country' => $sessionData['ip_country'] ?? null,
            'package_id' => $packageId,
            'is_featured' => $packageId ? true : false,
            'promoted_until' => $packageId ? now()->addDays($duration) : null,
        ]);
        
        // Debug logging
        \Log::info('Profile Created with Package', [
            'profile_id' => $profile->id,
            'ip_address' => $profile->ip_address,
            'ip_country' => $profile->ip_country,
            'session_ip' => $sessionData['ip_address'] ?? 'not set',
            'session_country' => $sessionData['ip_country'] ?? 'not set',
        ]);

        // Process images from stored paths
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
       
        // Process and save images from temporary paths
        foreach($sessionData['tempImagePaths'] as $key => $imagePath) {
            if (!file_exists($imagePath)) {
                continue; // Skip if temporary file no longer exists
            }

            $fileName = time() . rand(100,999);
            
            $manager = new ImageManager(new Driver());
            $img = $manager->read($imagePath);
            
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
            $thumbImg = $manager->read($imagePath);
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
                'is_main' => $key == $sessionData['mainImage'] || ($sessionData['mainImage'] == 0 && $key == 0)
            ]);
        }

        // ─── Videos ──────────────────────────────────────────────────────────
        // Same disk, parallel directory tree: uservideos/{user_id}/{profile_id}
        // Thumbnails (base64-captured client-side) decoded to JPEG and stored
        // beside the MP4 so the profile grid has a poster without the browser
        // having to decode frame 0 of every clip on first paint.
        $videoBasePath = "uservideos/{$profile->user_id}/{$profile->id}";
        if (!empty($sessionData['tempVideoPaths'])) {
            foreach (['uservideos', "uservideos/{$profile->user_id}", $videoBasePath] as $dir) {
                if (!$externalDisk->exists($dir)) {
                    $externalDisk->makeDirectory($dir, 0755, true);
                }
            }

            $vThumbs = $sessionData['videoThumbnails'] ?? [];
            foreach ($sessionData['tempVideoPaths'] as $vKey => $vMeta) {
                if (empty($vMeta['path']) || !file_exists($vMeta['path'])) {
                    continue; // Livewire temp file gone — skip rather than erroring the whole transaction
                }
                // Pick extension from mime; default .mp4 for anything odd.
                $ext = match ($vMeta['mime']) {
                    'video/webm'      => 'webm',
                    'video/quicktime' => 'mov',
                    default           => 'mp4',
                };
                $uuid     = (string) \Illuminate\Support\Str::uuid();
                $vidName  = $uuid . '.' . $ext;
                $thumbName = null;

                $externalDisk->put("{$videoBasePath}/{$vidName}", file_get_contents($vMeta['path']));

                // Thumbnail (optional — fall back to <video poster-less> if missing).
                // $vThumbs[$vKey] is now a FILENAME in storage/app/livewire-tmp,
                // not a base64 data URL (see setVideoThumbnail). Read, upload,
                // then delete the temp file so livewire-tmp doesn't accumulate
                // orphaned frame captures.
                if (!empty($vThumbs[$vKey])) {
                    $tmpName = basename($vThumbs[$vKey]);
                    if ($tmpName === $vThumbs[$vKey]) {
                        $tmpPath = storage_path('app/livewire-tmp/' . $tmpName);
                        if (is_file($tmpPath)) {
                            $thumbBytes = @file_get_contents($tmpPath);
                            if ($thumbBytes !== false && strlen($thumbBytes) > 0) {
                                $thumbName = $uuid . '.jpg';
                                $externalDisk->put("{$videoBasePath}/{$thumbName}", $thumbBytes);
                            }
                            @unlink($tmpPath);
                        }
                    }
                }

                ProfileVideo::create([
                    'user_id'         => $profile->user_id,
                    'profile_id'      => $profile->id,
                    'video'           => $vidName,
                    'video_thumbnail' => $thumbName,
                    'mime'            => $vMeta['mime'],
                    'size_bytes'      => $vMeta['size'] ?? null,
                    'is_main'         => false,
                    'video_order'     => $vKey,
                ]);
            }
        }
        
        // Attach languages
        $languages = [
            ['lang' => $sessionData['language1'] ?? null, 'level' => $sessionData['expert1'] ?? null],
            ['lang' => $sessionData['language2'] ?? null, 'level' => $sessionData['expert2'] ?? null],
            ['lang' => $sessionData['language3'] ?? null, 'level' => $sessionData['expert3'] ?? null],
            ['lang' => $sessionData['language4'] ?? null, 'level' => $sessionData['expert4'] ?? null],
            ['lang' => $sessionData['language5'] ?? null, 'level' => $sessionData['expert5'] ?? null],
        ];

        collect($languages)
            ->filter(fn($lang) => $lang['lang'])
            ->each(fn($lang) => UserLanguage::create([
                'user_id' => Auth::id(),
                'language_id' => $lang['lang'],
                'expert' => $lang['level'],
                'profile_id' => $profile->id
            ]));
        
        // Attach services
        if (isset($sessionData['services']) && is_array($sessionData['services'])) {
            collect($sessionData['services'])->each(fn($service) => 
                UserService::create([
                    'user_id' => Auth::id(),
                    'service_id' => $service,
                    'profile_id' => $profile->id
                ])
            );
        }
        
        DB::commit();

        // Clear session data
        session()->forget('new_profile_data_' . auth()->id());

        return $profile;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

private function createProfileRecord()
{

    return UsersProfile::create([
        'user_id' => Auth::id(),
        'name' => $this->name,
        'slug' => Str::slug($this->name),
        'listing' => $this->listing,
        'city' => $this->city,
        'about' => $this->aboutme,
        'phone' => str_replace(' ', '', $this->phone),
        'country_code' => $this->countrycode,
        'iswhatsapp' => $this->iswhatsapp,
        'istelegram' => $this->istelegram,
        'iswechat' => $this->iswechat,
        'issignal' => $this->issignal,
        'phone2' => str_replace(' ', '', $this->phone2),
        'country_code2' => $this->countrycode2,
        'iswhatsapp2' => $this->iswhatsapp2,
        'istelegram2' => $this->istelegram2,
        'iswechat2' => $this->iswechat2,
        'issignal2' => $this->issignal2,
        'website' => $this->website,
        'onlyfans' => $this->onlyfans,
        'gender' => $this->gender,
        'incall' => $this->incall ? true : false,
        'outcall' => $this->outcall ? true : false,
        'incallprice' => $this->incallprice,
        'incallcurr' => $this->incallcurr,
        'outcallprice' => $this->outcallprice,
        'outcallcurr' => $this->outcallcurr,
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
        'ip_address' => request()->ip(),
        'ip_country' => $this->getCountryFromIp(request()->ip()),
        'package_id' => null, // Free profile - no package assigned
    ]);
}

private function attachLanguages($profileId)
{
    $languages = [
        ['lang' => $this->language1, 'level' => $this->expert1],
        ['lang' => $this->language2, 'level' => $this->expert2],
        ['lang' => $this->language3, 'level' => $this->expert3],
        ['lang' => $this->language4, 'level' => $this->expert4],
        ['lang' => $this->language5, 'level' => $this->expert5],
    ];

    collect($languages)
        ->filter(fn($lang) => $lang['lang'])
        ->each(fn($lang) => UserLanguage::create([
            'user_id' => Auth::id(),
            'language_id' => $lang['lang'],
            'expert' => $lang['level'],
            'profile_id' => $profileId
        ]));
}

private function attachServices($profileId)
{
    collect($this->services)->each(fn($service) => 
        UserService::create([
            'user_id' => Auth::id(),
            'service_id' => $service,
            'profile_id' => $profileId
        ])
    );
}


private function linkProfileImages($profileId)
{
    ProfileImage::where('random', Session::get('random'))
        ->update(['profile_id' => $profileId]);
}

    private function saveLanguages($profileId)
    {
        $languages = [
            ['lang' => $this->language1, 'level' => $this->expert1],
            ['lang' => $this->language2, 'level' => $this->expert2],
            ['lang' => $this->language3, 'level' => $this->expert3],
            ['lang' => $this->language4, 'level' => $this->expert4],
            ['lang' => $this->language5, 'level' => $this->expert5],
        ];

        foreach($languages as $lang) {
            if($lang['lang']) {
                UserLanguage::create([
                    'user_id' => Auth::id(),
                    'language_id' => $lang['lang'],
                    'expert' => $lang['level'],
                    'profile_id' => $profileId
                ]);
            }
        }
    }

    public function render()
    {
        $currentCountry = function_exists('getCurrentCountry') ? getCurrentCountry() : null;

        $topCities = City::query()
            ->when($currentCountry, fn($q) => $q->where('country', $currentCountry->nicename))
            ->orderByDesc('is_featured')
            ->orderBy('feature_priority', 'asc')
            ->orderBy('name', 'asc')
            ->limit(6)
            ->get(['id', 'name', 'iso', 'country'])
            ->map(function ($city) {
                $currency = Currency::where('country', $city->country)->first();
                return [
                    'id' => $city->id,
                    'name' => $city->name,
                    'iso' => $city->iso,
                    'currency_code' => $currency ? $currency->code : 'USD',
                ];
            })
            ->values();

        return view('livewire.profile.users.new-profile', [
            'listings' => Listing::all(),
            'services' => Service::all(),
            'countries' => Country::all(),
            'user' => Auth::user(),
            'pimgs' => ProfileImage::where('user_id', Auth::id())
                        ->where('random', Session::get('random'))
                        ->get(),
            'genders' => Gender::all(),
            'currencies' => Currency::select('id', 'code', 'symbol')
                ->get()
                ->unique('code')
                ->sortBy('code')
                ->values(),
            'ethnicities' => Ethnicity::all(),
            'busts' => Bust::all(),
            'hairs' => HairColor::all(),
            'languages' => Language::all(),
            'topCities' => $topCities,
        ]);
    }

    public function searchCity($search)
    {
        return City::query()
            ->where(function($query) use ($search) {
                $query->where('name', 'like', $search . '%')
                      ->orWhere('name', 'like', '% ' . $search . '%');
            })
            ->select('id', 'name', 'iso')
            ->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3 
                END", 
                [$search . '%', '% ' . $search . '%']
            )
            ->limit(10)
            ->get();
    }

    /**
     * Get country name from IP address using ip-api.com
     */
    private function getCountryFromIp($ip)
    {
        // Skip for local/private IPs
        if ($ip === '127.0.0.1' || $ip === '::1' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
            return 'Local';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['country'] ?? 'Unknown';
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to get country from IP: " . $e->getMessage());
        }

        return 'Unknown';
    }

}