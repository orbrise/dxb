<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UsersProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\VerificationPhoto;
use Storage;

class VerifyPhoto extends Component
{
    public $id, $check;
    public $profileLink;
    public $verificationPhoto;
    public $verificationCode;

    public function mount($id)
    {
        $this->id = $id;
        $user = UsersProfile::with(['ggender', 'getcity'])
            ->where('id', $this->id)
            ->first();
        
        if (!$user) {
            return redirect()->route('new.profile');
        }

        $this->verificationCode = mt_rand(1000, 9999);
        UsersProfile::where('id', $this->id)->update(['photo_code' => $this->verificationCode]);
        $this->check = (int)$user->is_active;
        $this->profileLink = "{$user->ggender->name}-escorts-in-{$user->getcity->name}/{$user->id}/{$user->slug}";
    }


    public function render()
    {
        $user = UsersProfile::where('id', $this->id)->first();

        $check = VerificationPhoto::where('user_id', auth()->user()->id)
        ->where('profile_id', $this->id)
        ->orderBy('id', 'desc')
        ->first();

        if(!empty($check)) {
            if($check->status == 'pending'){
                session()->flash('success', 'Your verification photo is pending. Our team will review it shortly.');
                return view('livewire.profile.users.user-dashboard', compact('user'));
            } 
            
            if($check->status == 'approved'){
                session()->flash('success', 'Your verification photo is approved.');
                return view('livewire.profile.users.user-dashboard', compact('user'));
            }
        }

        
        $photo_code  =$this->verificationCode;
        $this->check = $user->is_active;
        return view('livewire.verify-photo', compact('user', 'photo_code'));
    }


    public function uploadVerificationPhoto($photoData)
    {
        dd([
            'received_length' => strlen($photoData),
            'data_start' => substr($photoData, 0, 50),
            'user_id' => $user->user_id,
            'profile_id' => $this->id
        ]);
        $user = UsersProfile::findOrFail($this->id);
        \Log::info('Received photo data length: ' . strlen($photoData));

        // Clean base64 data
        $cleanData = str_replace('data:image/jpeg;base64,', '', $photoData);
        $cleanData = str_replace(' ', '+', $cleanData);
        
        // Decode to binary
        $imageData = base64_decode($cleanData);
        \Log::info('Binary data length: ' . strlen($imageData));

        // Set paths
        $filename = time() . '_verify.jpg'; 
        $relativePath = "userimages/{$user->user_id}/verification/";
        $fullPath = storage_path("app/public/" . $relativePath);
        \Log::info('Saved file size: ' . filesize($fullPath));

        // Create directory
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        // Save file
        file_put_contents($fullPath . $filename, $imageData);
    
        // Create record
        VerificationPhoto::create([
            'user_id' => $user->user_id,
            'profile_id' => $this->id,
            'photo' => $filename,
            'status' => 'pending'
        ]);
    
        $this->dispatch('photoUploaded');
    }


}
