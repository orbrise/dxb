<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DefaultSeoSetting;

class DefaultSeoSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'name' => 'global',
                'title' => 'Premium Escort Services | DXB Escorts',
                'description' => 'Find premium escort services in Dubai and UAE. Professional, discreet, and reliable escort companions for all occasions.',
                'keywords' => 'escort services, dubai escorts, premium escorts, professional companions',
                'content' => 'Welcome to the finest escort services in the region. We provide professional and discreet companionship services.',
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'homepage',
                'title' => 'DXB Escorts - Premium Escort Services in Dubai & UAE',
                'description' => 'Discover premium escort services in Dubai and across UAE. Professional companions for business, social events, and private occasions.',
                'keywords' => 'dubai escorts, uae escorts, premium escort services, professional companions, dubai',
                'content' => 'Your premier destination for professional escort services in Dubai and UAE.',
                'priority' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'escorts',
                'title' => '{gender} Escorts in {city} - Premium Companions | DXB Escorts',
                'description' => 'Premium {gender} escort services in {city}. Professional, discreet companions for all occasions in {country}.',
                'keywords' => '{gender} escorts, {city} escorts, escort services {city}, professional companions {city}',
                'content' => 'Discover our exclusive collection of {gender} escorts in {city}.',
                'priority' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'city-pages',
                'title' => 'Escort Services in {city} - Premium Companions',
                'description' => 'Find premium escort services in {city}, {country}. Professional companions for business and leisure.',
                'keywords' => 'escorts {city}, escort services {city}, companions {city}',
                'content' => 'Professional escort services available in {city}.',
                'priority' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'login',
                'title' => 'Login to Your Account | DXB Escorts',
                'description' => 'Login to your account to access premium escort services and manage your profile.',
                'keywords' => 'login, account, access, member area',
                'content' => 'Secure login to access your account and premium features.',
                'priority' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'register',
                'title' => 'Create Your Account | Join DXB Escorts',
                'description' => 'Register for a free account to access premium escort services and exclusive features.',
                'keywords' => 'register, signup, create account, join, membership',
                'content' => 'Join our exclusive community of premium service providers and clients.',
                'priority' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'contact',
                'title' => 'Contact Us | Get in Touch with DXB Escorts',
                'description' => 'Contact our team for inquiries about premium escort services, support, or partnership opportunities.',
                'keywords' => 'contact, support, inquiries, help, customer service',
                'content' => 'Get in touch with our professional team for any assistance.',
                'priority' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'about',
                'title' => 'About Us | Premium Escort Services | DXB Escorts',
                'description' => 'Learn about our premium escort services, our commitment to quality, and why we are the trusted choice in Dubai.',
                'keywords' => 'about us, premium services, dubai escorts, company info',
                'content' => 'Discover our story and commitment to excellence in service provision.',
                'priority' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'profile-dashboard',
                'title' => 'My Dashboard | Manage Your Profile | DXB Escorts',
                'description' => 'Manage your profile, view bookings, and access exclusive features in your personal dashboard.',
                'keywords' => 'dashboard, profile, manage account, bookings, settings',
                'content' => 'Your personal space to manage all account activities.',
                'priority' => 6,
                'is_active' => true,
            ]
        ];

        foreach ($defaultSettings as $setting) {
            DefaultSeoSetting::updateOrCreate(
                ['name' => $setting['name']],
                $setting
            );
        }
        
        $this->command->info('Default SEO settings have been created successfully!');
    }
}
