<?php
namespace App\Livewire\Auth;

use Livewire\Component;
use Str;
use App\Models\User;
use Mail;
use Hash;
use App\Mail\NewUser;
use App\Models\Country;

class EscortRegister extends Component
{
    public $name;
    public $email;
    public $password;
    public $type;
    public $password_confirmation;
    public $terms = false;
    public $countrycode = '';
    public $phone = '';

    protected $rules = [
        'name' => 'required|min:5|max:50',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'type' => 'required|in:2,3',
        'terms' => 'accepted',
        'countrycode' => 'required',
        'phone' => 'required|min:6|max:20'
    ];

    protected $messages = [
        'name.required' => 'Display name is required',
        'name.min' => 'Name must be at least 5 characters',
        'email.unique' => 'This email is already registered',
        'type.in' => 'Please select valid account type',
        'terms.accepted' => 'You must accept the Terms and Conditions'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function registerEsc()
    {
        \Log::info('Registration started', [
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'terms' => $this->terms
        ]);
        
        $validatedData = $this->validate();
        
        \Log::info('Validation passed', $validatedData);
        
        try {
            $random = Str::random(40);
            $userIp = $this->getClientIp();
            $userCountry = $this->getCountryFromIp($userIp);
            
            \Log::info("Escort Registration - IP: {$userIp}, Country: {$userCountry}");
            
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'verification_code' => $random,
                'type' => 3,
                'agency_type' => $this->type,
                'status' => 'pending',
                'registration_ip' => $userIp,
                'registration_country' => $userCountry,
                'country_code' => $this->countrycode,
                'phone' => str_replace(' ', '', $this->phone)
            ]);
            
            \Log::info('User created', ['user_id' => $user->id]);

            Mail::to($this->email)->send(new NewUser([
                'name' => $user->name,
                'random' => $random,
                'email' => $user->email,
            ]));
            
            \Log::info('Email sent');

            $this->reset();
            
            return redirect()->route('sign-in')->with('message', 'Registration successful! Please check your email to verify your account.');

        } catch (\Exception $e) {
            \Log::error('Registration Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Registration failed: ' . $e->getMessage());
        }
    }
    
    private function getCountryFromIp($ip)
    {
        // Skip for local/private IPs
        if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }
        
        // Check if it's a private IP
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'Local';
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get("http://ip-api.com/json/{$ip}");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'success') {
                    return $data['country'] ?? 'Unknown';
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to get country from IP {$ip}: " . $e->getMessage());
        }

        return 'Unknown';
    }
    
    private function getClientIp()
    {
        // Check for proxy headers first
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Common proxy header
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_CLIENT_IP',            // General proxy
            'REMOTE_ADDR'                // Direct connection
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                // X-Forwarded-For can contain multiple IPs, take the first one
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                // Validate it's a proper IP
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return request()->ip();
    }

    public function render()
    {
        return view('livewire.auth.escort-register', [
            'countries' => Country::orderBy('nicename')->get()
        ]);
    }
}
