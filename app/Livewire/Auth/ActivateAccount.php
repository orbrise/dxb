<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivateAccount extends Component
{
    public $email, $code;
    public $done = '';

    public function mount($email, $code)
    {
        $this->email = $email;
        $this->code = $code;
        
        $check = User::where(['email' => $this->email, 'verification_code' => $this->code])->first();

        if(!empty($check)){
            User::where('email', $this->email)->update(['email_verified_at' => date('Y-m-d'), 'verified' => 1, 'status' => 'active']);
            
            // Log the user in
            Auth::login($check);
            
            // Redirect to account settings page after successful verification
            return redirect()->route('user.account')->with('success', 'Your account has been verified successfully!');
        } else {
            $this->done = 'error';
        }
    }


    public function render()
    {
        return view('livewire.auth.activate-account', ['done' => $this->done]);
    }
}
