<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NewsletterSubscription;
use App\Models\NewsletterGender;
use App\Models\UsersProfile;
use App\Models\Review;
use App\Models\Question;
use App\Models\User;
use App\Mail\WeeklyNewsletter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWeeklyNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:send-weekly 
                            {--days=7 : Number of days to look back for content}
                            {--all : Send all content regardless of date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly newsletter to all subscribed users with latest content from their selected city and categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting weekly newsletter sending...');
        
        // Get all active newsletter subscriptions
        $subscriptions = NewsletterSubscription::with(['user', 'city'])
            ->whereHas('user', function($query) {
                $query->where('email_verified_at', '!=', null);
            })
            ->get()
            ->groupBy('user_id');
        
        $totalSent = 0;
        $totalFailed = 0;
        
        foreach ($subscriptions as $userId => $userSubscriptions) {
            $user = $userSubscriptions->first()->user;
            
            if (!$user || !$user->email) {
                $this->warn("Skipping user ID {$userId} - no valid email");
                continue;
            }
            
            // Get user's selected genders/categories
            $selectedGenders = NewsletterGender::where('user_id', $userId)
                ->pluck('gender')
                ->toArray();
            
            if (empty($selectedGenders)) {
                $selectedGenders = ['female']; // Default to female if none selected
            }
            
            // Convert gender names to IDs (female=1, male=2, shemale=3)
            $genderMap = [
                'female' => 1,
                'male' => 2,
                'shemale' => 3
            ];
            
            $genderIds = array_map(function($gender) use ($genderMap) {
                return $genderMap[strtolower($gender)] ?? 1;
            }, $selectedGenders);
            
            // Get all cities the user is subscribed to
            $cityIds = $userSubscriptions->pluck('city_id')->toArray();
            
            // Get content for the newsletter
            $newsletterData = $this->getNewsletterContent($cityIds, $genderIds, $selectedGenders);
            
            // Debug: Show what we got
            $this->line("  Reviews: " . $newsletterData['reviews']->count());
            $this->line("  Listings: " . $newsletterData['listings']->count());
            $this->line("  Questions: " . $newsletterData['questions']->count());
            
            if ($newsletterData['reviews']->count() === 0 && $newsletterData['listings']->count() === 0 && $newsletterData['questions']->count() === 0) {
                $this->info("Skipping {$user->email} - no new content");
                continue;
            }
            
            try {
                Mail::to($user->email)->send(new WeeklyNewsletter($user, $newsletterData));
                $this->info("✓ Sent newsletter to {$user->email}");
                $totalSent++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$user->email}: " . $e->getMessage());
                Log::error("Newsletter send failed for {$user->email}: " . $e->getMessage());
                $totalFailed++;
            }
        }
        
        $this->info("\n=== Newsletter Sending Complete ===");
        $this->info("Successfully sent: {$totalSent}");
        $this->error("Failed: {$totalFailed}");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get newsletter content for specified cities and genders
     *
     * @param array $cityIds
     * @param array $genderIds - Gender IDs (1=female, 2=male, 3=shemale)
     * @param array $genderNames - Gender names for display
     * @return array
     */
    private function getNewsletterContent($cityIds, $genderIds, $genderNames)
    {
        // Check if we should send all content or limit by date
        $sendAll = $this->option('all');
        $days = $this->option('days') ?? 7;
        $dateLimit = Carbon::now()->subDays($days);
        
        $this->line("  DEBUG: Cities: " . implode(',', $cityIds));
        $this->line("  DEBUG: Gender IDs: " . implode(',', $genderIds));
        $this->line("  DEBUG: Send all: " . ($sendAll ? 'YES' : 'NO'));
        
        // DEBUG: Check what genders exist in database
        $allGenders = UsersProfile::whereIn('city', $cityIds)->distinct()->pluck('gender')->toArray();
        $this->line("  DEBUG: Available genders in DB: " . implode(',', $allGenders));
        
        // Get latest reviews
        $reviewsQuery = Review::with(['profile.coverimg', 'profile.singleimg', 'profile.getcity'])
            ->whereHas('profile', function($query) use ($cityIds, $genderIds) {
                $query->whereIn('city', $cityIds)
                      ->whereIn('gender', $genderIds);
                      // Removed is_active check - profiles table might not use this field
            })
            ->where('status', 'active');
        
        if (!$sendAll) {
            $reviewsQuery->where('created_at', '>=', $dateLimit);
        }
        
        $reviews = $reviewsQuery->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get new listings
        $listingsQuery = UsersProfile::with(['coverimg', 'singleimg', 'getcity', 'reviews'])
            ->whereIn('city', $cityIds)
            ->whereIn('gender', $genderIds);
            // Removed is_active = 1 check
        
        if (!$sendAll) {
            $listingsQuery->where('created_at', '>=', $dateLimit);
        }
        
        $listings = $listingsQuery->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
        
        $this->line("  DEBUG: Listings SQL: " . $listingsQuery->toSql());
        $this->line("  DEBUG: Listings bindings: " . json_encode($listingsQuery->getBindings()));
        // Get latest answered questions
        $questionsQuery = Question::with(['profile.coverimg', 'profile.singleimg', 'profile.getcity'])
            ->whereHas('profile', function($query) use ($cityIds, $genderIds) {
                $query->whereIn('city', $cityIds)
                      ->whereIn('gender', $genderIds);
                      // Removed is_active check
            })
            ->whereNotNull('answer')
            ->where('answer_status', 'published');
        
        if (!$sendAll) {
            $questionsQuery->where('updated_at', '>=', $dateLimit);
        }
        
        $questions = $questionsQuery->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return [
            'reviews' => $reviews,
            'listings' => $listings,
            'questions' => $questions,
            'cityNames' => \App\Models\City::whereIn('id', $cityIds)->pluck('name')->toArray(),
            'genders' => $genderNames, // Use names for display, not IDs
        ];
    }
}
