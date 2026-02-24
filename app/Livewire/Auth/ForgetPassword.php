<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Mail\ForgetPassword as Forgetpass;
use Str;
use Mail;
use App\Models\MailSettings;

class ForgetPassword extends Component
{
    public $email;

    public function render()
    {
        return view('livewire.auth.forget-password');
    }

    public function forgetPassword() {
        $random = Str::random(40);
        $check = User::where("email", $this->email)->first();
        if(!empty($check)) {
            User::where('email', $this->email)->update(['remember_token' => $random]);
            
            // Check if password reset emails are enabled in admin settings
            if (MailSettings::shouldSendEmail('forget_password')) {
                try {
                    Mail::to($this->email)->send(new Forgetpass([
                        'name' => $check->name,
                        'random' => $random,
                        'email' => $check->email,
                    ]));
                    session()->flash('success', 'Verification email sent to your inbox, please check your inbox.');
                } catch (\Exception $e) {
                    // Log the error but still show success to user (token is saved)
                    \Log::error('Password reset email failed: ' . $e->getMessage());
                    session()->flash('success', 'Password reset link generated. Please contact support if you don\'t receive the email.');
                }
            } else {
                session()->flash('success', 'Password reset link generated. Email notifications are currently disabled.');
            }
            
           $this->reset();
        } else {
            session()->flash('error', 'Error! user not found.');
        }
    }
}
