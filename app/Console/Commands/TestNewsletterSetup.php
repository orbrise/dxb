<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsletterSubscription;
use App\Models\NewsletterGender;
use App\Models\User;
use App\Models\City;
use Carbon\Carbon;

class TestNewsletterSetup extends Command
{
    protected $signature = 'newsletter:test-setup';
    protected $description = 'Test newsletter system setup and configuration';

    public function handle()
    {
        $this->info('🔍 Testing Newsletter System Setup...');
        $this->newLine();
        
        // Test 1: Check database tables
        $this->info('1. Checking database tables...');
        try {
            $subscriptionsCount = NewsletterSubscription::count();
            $gendersCount = NewsletterGender::count();
            $this->line("   ✓ newsletter_subscriptions table exists ({$subscriptionsCount} records)");
            $this->line("   ✓ newsletter_genders table exists ({$gendersCount} records)");
        } catch (\Exception $e) {
            $this->error('   ✗ Database tables missing! Run: php artisan migrate');
            return Command::FAILURE;
        }
        $this->newLine();
        
        // Test 2: Check subscribers
        $this->info('2. Checking subscribers...');
        $subscribers = NewsletterSubscription::with(['user', 'city'])
            ->get()
            ->groupBy('user_id');
            
        if ($subscribers->count() > 0) {
            $this->line("   ✓ Found {$subscribers->count()} subscribed users");
            foreach ($subscribers->take(5) as $userId => $subs) {
                $user = $subs->first()->user;
                $cities = $subs->pluck('city.name')->implode(', ');
                $this->line("     - {$user->email} → {$cities}");
            }
        } else {
            $this->warn('   ⚠ No subscribers found yet');
            $this->line('     Add via: /my-account/newsletter/edit');
        }
        $this->newLine();
        
        // Test 3: Check mail configuration
        $this->info('3. Checking mail configuration...');
        $mailDriver = config('mail.default');
        $mailHost = config('mail.mailers.' . $mailDriver . '.host');
        $mailFrom = config('mail.from.address');
        
        $this->line("   ✓ Mail driver: {$mailDriver}");
        $this->line("   ✓ Mail host: {$mailHost}");
        $this->line("   ✓ From address: {$mailFrom}");
        $this->newLine();
        
        // Test 4: Check for recent content
        $this->info('4. Checking for recent content (last 7 days)...');
        $oneWeekAgo = Carbon::now()->subWeek();
        
        $recentReviews = \App\Models\Review::where('created_at', '>=', $oneWeekAgo)
            ->where('status', 'active')
            ->count();
            
        $recentProfiles = \App\Models\UsersProfile::where('created_at', '>=', $oneWeekAgo)
            ->where('is_active', 1)
            ->count();
            
        $recentQuestions = \App\Models\Question::whereNotNull('answer')
            ->where('updated_at', '>=', $oneWeekAgo)
            ->count();
        
        $this->line("   ✓ Recent reviews: {$recentReviews}");
        $this->line("   ✓ Recent profiles: {$recentProfiles}");
        $this->line("   ✓ Recent questions: {$recentQuestions}");
        
        if ($recentReviews == 0 && $recentProfiles == 0 && $recentQuestions == 0) {
            $this->warn('   ⚠ No recent content - newsletters will be skipped');
        }
        $this->newLine();
        
        // Test 5: Check scheduler
        $this->info('5. Checking scheduler configuration...');
        $this->line('   Run: php artisan schedule:list');
        $this->line('   Look for: newsletter:send-weekly');
        $this->newLine();
        
        // Test 6: Preview newsletter data
        if ($subscribers->count() > 0) {
            $this->info('6. Preview newsletter data for first subscriber...');
            $firstSub = $subscribers->first();
            $user = $firstSub->first()->user;
            $cityIds = $firstSub->pluck('city_id')->toArray();
            $genders = NewsletterGender::where('user_id', $user->id)->pluck('gender')->toArray();
            
            if (empty($genders)) {
                $genders = ['female'];
            }
            
            $this->line("   User: {$user->email}");
            $this->line("   Cities: " . implode(', ', City::whereIn('id', $cityIds)->pluck('name')->toArray()));
            $this->line("   Genders: " . implode(', ', $genders));
            
            // Get content
            $reviews = \App\Models\Review::with(['profile.coverimg', 'profile.getcity'])
                ->whereHas('profile', function($query) use ($cityIds, $genders) {
                    $query->whereIn('city', $cityIds)
                          ->whereIn('gender', $genders)
                          ->where('is_active', 1);
                })
                ->where('status', 'active')
                ->where('created_at', '>=', $oneWeekAgo)
                ->count();
                
            $this->line("   → Would include {$reviews} reviews");
        }
        $this->newLine();
        
        // Summary
        $this->info('📊 Summary:');
        $allGood = true;
        
        if ($subscriptionsCount == 0) {
            $this->warn('⚠ Action needed: Add subscribers via /my-account/newsletter/edit');
            $allGood = false;
        } else {
            $this->line('✓ Subscribers configured');
        }
        
        if ($recentReviews == 0 && $recentProfiles == 0 && $recentQuestions == 0) {
            $this->warn('⚠ Note: No recent content - add some or wait for activity');
            $allGood = false;
        } else {
            $this->line('✓ Recent content available');
        }
        
        $this->newLine();
        $this->info('🚀 Next Steps:');
        $this->line('1. Set up cron: * * * * * cd /path/to/project && php artisan schedule:run');
        $this->line('2. Test send: php artisan newsletter:send-weekly');
        $this->line('3. Check logs: tail -f storage/logs/laravel.log');
        $this->newLine();
        
        if ($allGood) {
            $this->info('✅ Newsletter system is ready!');
            return Command::SUCCESS;
        } else {
            $this->warn('⚠️  Newsletter system needs configuration (see above)');
            return Command::SUCCESS;
        }
    }
}
