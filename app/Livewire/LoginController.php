<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.app')]
class LoginController extends Component
{
    public $email;
    public $password;
    public $remember = false;
    public $errorMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6'
    ];

    protected $messages = [
        'email.required' => 'Email address is required',
        'email.email' => 'Please enter a valid email address',
        'password.required' => 'Password is required',
        'password.min' => 'Password must be at least 6 characters'
    ];

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirect(route('user.account'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.login-controller');
    }

    public function loginNow()
    {
        $this->errorMessage = '';
        
        $this->validate();

        // Debug: Check if user exists
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            $this->errorMessage = 'No account found with this email address.';
            $this->reset('password');
            return;
        }
        
        if ($user->verified != 1) {
            $this->errorMessage = 'Your account is not verified. Please check your email.';
            $this->reset('password');
            return;
        }

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
            'verified' => 1
        ], $this->remember)) {
            
            session()->regenerate();
            
            if (Auth::user()->type == 1) {
                if (Auth::user()->getprofile->count() > 0) {
                    session()->flash('success', 'Welcome back!');
                    return redirect()->to("my-profile/".Auth::user()->name."/".Auth::user()->id);
                }
                session()->flash('info', 'Please complete your profile');
                return redirect()->route('new.profile');
            }

            return redirect()->intended('/my-account');
        }

        // Login failed - password wrong
        $this->errorMessage = 'Incorrect password. Please try again.';
        $this->reset('password');
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }
}