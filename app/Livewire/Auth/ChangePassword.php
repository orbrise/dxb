<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Hash;

class ChangePassword extends Component
{

    public $email, $code, $password, $password_confirmation;

    public function mount($email, $code)
    {
    $this->email = $email;
    $this->code = $code;
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }

    public function changePass() {

        $this->validate([
            'password' => 'required|min:6|max:100|same:password_confirmation',
            'password_confirmation' => 'required',
        ]);

        User::where('email', $this->email)->update(['remember_token' => '', 'password' => Hash::make($this->password)]);
        $this->reset();
        return redirect()->route('sign-in')->with('message', 'Success! Your password has been changed');
    }
}
