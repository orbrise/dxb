<?php

namespace App\Livewire;

use Livewire\Component;
use Hash;
use Illuminate\Support\Str;
use App\Mail\NewUser;
use App\Models\User;
use Mail;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Models\MailSettings;
use App\Models\Country;

class RegisterController extends Component
{
    public $name, $email, $password;
    public $countrycode = '';
    public $phone = '';
    public $errorMessage = '';

    protected $rules = [
        'name' => 'required|min:5|unique:users,name',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|max:100',
        'countrycode' => 'required',
        'phone' => 'required|min:6|max:20'
    ];

    public function submitForm() 
    {
        $validatedData = $this->validate();

        try {
            $random = Str::random(40);
            $userIp = $this->getClientIp();
            $userCountry = $this->getCountryFromIp($userIp);
            
            Log::info("Registration attempt - IP: {$userIp}, Country: {$userCountry}");
            
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'verification_code' => $random,
                'type' => 2,
                'verified' => 0,
                'registration_ip' => $userIp,
                'registration_country' => $userCountry,
                'country_code' => $this->countrycode,
                'phone' => str_replace(' ', '', $this->phone)
            ]);

            if ($user) {
                // Check if account creation emails are enabled in admin settings
                if (MailSettings::shouldSendEmail('account_create')) {
                    Mail::to($this->email)->send(new NewUser([
                        'name' => $user->name,
                        'random' => $random,
                        'email' => $user->email,
                    ]));
                }

                $this->reset(['name', 'email', 'password']);
                session()->flash('success', 'Success! Check your inbox and activate your account before login');
                return;
            }

            throw new \Exception('User creation failed');

        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());
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
            Log::warning("Failed to get country from IP {$ip}: " . $e->getMessage());
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
        return view('livewire.register-controller', [
            'countries' => Country::orderBy('nicename')->get()
        ]);
    }
}