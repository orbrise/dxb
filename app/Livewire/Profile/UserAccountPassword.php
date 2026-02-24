<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAccountPassword extends Component
{
    public $current_password;
    public $password;
    
    protected $rules = [
        'current_password' => 'required|min:5|max:80',
        'password' => 'required|min:5|max:80'
    ];

    public function updatePassword()
    {
        $this->validate();

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        session()->flash('message', 'Password updated successfully!');
        $this->reset(['current_password', 'password']);
    }

    public function render()
    {
        return view('livewire.profile.user-account-password');
    }
}