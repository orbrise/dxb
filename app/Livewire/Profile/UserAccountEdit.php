<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;

class UserAccountEdit extends Component
{
    use WithFileUploads;

    public $email;
    public $display_name;
    public $about_me;
    public $account_type;
    public $avatar;
    public $remove_avatar = false;
    public $countrycode = '';
    public $phone = '';

    public function mount()
    {
        $user = Auth::user();
        $this->email = $user->email;
        $this->display_name = $user->name;
        $this->about_me = $user->about;
        $this->account_type = $user->type;
        $this->countrycode = $user->country_code ?? '';
        $this->phone = $user->phone ?? '';
    }

    protected $rules = [
        'display_name' => 'required|max:191',
        'about_me' => 'nullable',
        'account_type' => 'required|in:2,3',
        'avatar' => 'nullable|image|max:4096',
        'countrycode' => 'nullable',
        'phone' => 'nullable|max:20'
    ];

    /**
     * Calculate profile completion percentage
     */
    public function getProfileCompletionProperty()
    {
        $user = Auth::user();
        $fields = [];
        $weights = [];
        
        // Define fields and their weights (importance)
        // Email - required, always filled (10%)
        $fields['email'] = !empty($user->email);
        $weights['email'] = 10;
        
        // Display name (20%)
        $fields['display_name'] = !empty($this->display_name) || !empty($user->name);
        $weights['display_name'] = 20;
        
        // Account type (15%) - check for valid values 2 or 3
        $accountType = $this->account_type ?? $user->type;
        $fields['account_type'] = in_array($accountType, [2, 3, '2', '3']);
        $weights['account_type'] = 15;
        
        // About me (20%)
        $fields['about_me'] = !empty($this->about_me) || !empty($user->about);
        $weights['about_me'] = 20;
        
        // Profile photo (20%)
        $fields['avatar'] = !empty($user->avatar) || $this->avatar;
        $weights['avatar'] = 20;
        
        // Phone number (15%)
        $fields['phone'] = (!empty($this->phone) || !empty($user->phone)) && (!empty($this->countrycode) || !empty($user->country_code));
        $weights['phone'] = 15;
        
        $completedWeight = 0;
        $totalWeight = array_sum($weights);
        
        foreach ($fields as $field => $isComplete) {
            if ($isComplete) {
                $completedWeight += $weights[$field];
            }
        }
        
        $percentage = round(($completedWeight / $totalWeight) * 100);
        
        // Get incomplete fields for suggestions
        $incomplete = [];
        if (!$fields['display_name']) $incomplete[] = 'Display Name';
        if (!$fields['account_type']) $incomplete[] = 'Account Type';
        if (!$fields['about_me']) $incomplete[] = 'About Me';
        if (!$fields['avatar']) $incomplete[] = 'Profile Photo';
        if (!$fields['phone']) $incomplete[] = 'Phone Number';
        
        return [
            'percentage' => $percentage,
            'incomplete' => $incomplete,
            'isComplete' => $percentage >= 100
        ];
    }
 
    public function save()
    {
        $validated = $this->validate();
        
        $user = Auth::user();
 
        if($this->avatar) {
            $avatarPath = $this->avatar->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        if($this->remove_avatar) {
            $user->avatar = null;
        }

        $user->name = $this->display_name;
        $user->about = $this->about_me;
        $user->type = $this->account_type;
        $user->country_code = $this->countrycode;
        $user->phone = str_replace(' ', '', $this->phone);
        $user->save();

        session()->flash('message', 'Account updated successfully.');
        
        return redirect()->route('user.account.edit');
    }

    public function removePhoto()
    {
        $user = Auth::user();
        $user->avatar = null;
        $user->save();
        
        session()->flash('message', 'Photo removed successfully.');
        
        return redirect()->route('user.account.edit');
    }

    public function render()
    {
        return view('livewire.profile.user-account-edit', [
            'countries' => Country::orderBy('nicename')->get()
        ]);
    }
}