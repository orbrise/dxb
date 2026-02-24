<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UsersProfile;
use App\Models\City;
use App\Models\ProfileImage;
use App\Models\Review;
use App\Models\Question;
use App\Models\Country;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Report;
use App\Models\ProfileVisit;
use App\Models\PhoneClick;
use App\Models\User;
use App\Services\CacheService;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Cache;
 
class ProfileDetails extends Component
{
    public  $city,$gender, $user, $images, $star, $review, $profileid, $question, $email, $msg, $code, $phone, $cityname;
    public $showReviewModal = false;
    public $reportType;
public $reportDescription;


    public function mount( $city = '', $gender= '', $id=''){

        $this->profileid = $id;
        if(!empty($gender)){
          $this->gender = $gender;
        }
     
        if(!empty($city)){
            // Use cached city lookup
            $c = Cache::remember("cache:city:name:" . strtolower($city), 3600, function() use ($city) {
                return City::where('name', $city)->first();
            });
            if ($c) {
                $this->selectedcity = $city;
                $this->city = $c->id;
                $this->cityname = $c->name;
            } else {
                \Log::warning('City not found in ProfileDetails', ['city' => $city]);
            }
        } 

        // Use cached profile data
        $up = CacheService::getProfileDetail($id);
        $this->user = $up;
        
        // Track profile view
        if ($up) {
            $this->trackProfileView($id);
        }
        
        // Get ALL images for this profile (cached)
        $this->images = CacheService::getProfileImages($id);
        $this->code = "971";
        $this->dispatch('profile-loaded');
    }
    
    /**
     * Track profile view (IP-based, once per 24 hours)
     */
    protected function trackProfileView($profileId)
    {
        try {
            // Use IP-based tracking - only counts once per IP per 24 hours
            $recorded = ProfileVisit::recordVisit($profileId, request());
            
            if ($recorded) {
                \Log::info('Profile view tracked (unique)', ['profile_id' => $profileId, 'ip' => request()->ip()]);
            } else {
                \Log::debug('Profile view skipped (duplicate)', ['profile_id' => $profileId, 'ip' => request()->ip()]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to track profile view', [
                'profile_id' => $profileId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Track phone number click
     */
    public function trackPhoneClick()
    {
        try {
            // Use date-based tracking with IP deduplication
            $recorded = PhoneClick::recordClick($this->profileid, request());
            
            if ($recorded) {
                \Log::info('Phone click tracked (unique)', ['profile_id' => $this->profileid, 'ip' => request()->ip()]);
            } else {
                \Log::debug('Phone click skipped (duplicate)', ['profile_id' => $this->profileid, 'ip' => request()->ip()]);
            }
        } catch (\Exception $e) {
            // Fallback to old method if new table doesn't exist yet
            UsersProfile::where('id', $this->profileid)->increment('phone_clicks');
            \Log::error('Failed to track phone click with new system', [
                'profile_id' => $this->profileid,
                'error' => $e->getMessage()
            ]);
        }
        
        $this->dispatch('phoneClickTracked');
    }

    public function openReviewModal()
{
    $this->showReviewModal = true;
}


    public function render()
    {
        $user = $this->user;
        
        // Use cached images
        $images = CacheService::getProfileImages($this->profileid);
            
        // Use cached profile data
        $profile = CacheService::getProfileDetail($this->profileid);
      
        if(auth()->check()){
            // User's own review - not cached as it's user-specific
            $review = Review::where("user_id", auth()->user()->id)
                           ->where("profile_id", $this->profileid)
                           ->first();
        } else {
            $review = '';
        }
        
        $rev = !empty($review);

        // Use cached countries
        $countries = CacheService::getCountries();
        
        // Use cached reviews and questions
        $reviews = CacheService::getProfileReviews($this->profileid);
        $questions = CacheService::getProfileQuestions($this->profileid);
        
        return view('livewire.profile-details', compact("user", "images", "rev", "countries",'reviews','questions','profile'));
    }

    public function postreview()
    {
        $this->validate([
            'star' => 'required|numeric|min:1|max:5',
            'review' => 'required|min:10'
        ]);
    
        Review::create([
            'user_id' => auth()->id(),
            'profile_id' => $this->profileid,
            'review' => $this->review,
            'star' => $this->star,
            'status' => 'pending'
        ]);
    
        $this->reset(['star', 'review']);
        $this->showReviewModal = false;
        session()->flash('rmessage', 'Review Posted successfully.');
    }

    public function askquestion(){

        $q = new Question;
        $q->user_id = auth()->user()->id;
        $q->profile_id = $this->profileid;
        $q->question = $this->question;
        $q->status = 0;
        $q->save();
        return session()->flash('questionmsg', 'We will send an email when/if it is answered.');
    }

    public function sendmsg(){

        $p = UsersProfile::find($this->profileid);
        
        // Get the profile owner's user_id
        $profileOwnerId = $p->user_id;
        
        // Check if current user is logged in for two-way chat
        if (auth()->check()) {
            $senderId = auth()->id();
            
            // Don't allow messaging yourself
            if ($senderId === $profileOwnerId) {
                session()->flash('sendmsg_error', 'You cannot message yourself.');
                return;
            }
            
            // Create or get existing conversation
            $conversation = Conversation::getOrCreate($senderId, $profileOwnerId);
            
            // Create the message in the new chat system
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'message' => $this->msg,
                'status' => 'sent',
                // Keep legacy fields for backward compatibility
                'user_email' => auth()->user()->email,
                'profile_id' => $this->profileid,
                'code' => $this->code,
                'phone' => $this->phone,
            ]);
            
            // Update conversation last_message_at
            $conversation->update(['last_message_at' => now()]);
            
            // Broadcast to the profile owner for real-time notification
            broadcast(new NewChatMessage($message, $profileOwnerId))->toOthers();
            
            session()->flash('sendmsg', 'Your message has been sent to '.$p->name.'. You can continue the conversation in your Messages.');
        } else {
            // Guest user - save message in legacy format (one-way inquiry)
            $m = new Message;
            $m->user_email = $this->email;
            $m->profile_id = $this->profileid;
            $m->message = $this->msg;
            $m->code = $this->code;
            $m->phone = $this->phone;
            $m->save();
            
            session()->flash('sendmsg', 'Your message has been sent to '.$p->name.'. Please login to continue the conversation.');
        }
         
        $this->dispatch('closeMessageModal');
    }

    public function nextescort() {
       
        $next = UsersProfile::where('id', '>', $this->profileid)->first();

        
        return redirect($this->gender."-escorts-in-".$this->cityname."/".$next->id."/".$this->user->name);
    }

    public function submitReport()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            session()->flash('report_error', 'You must be logged in to report a profile.');
            return;
        }

        // Validate the report
        $this->validate([
            'reportType' => 'required|in:fake,spam,inappropriate,other',
            'reportDescription' => 'required|min:10|max:1000',
        ], [
            'reportType.required' => 'Please select a reason for reporting.',
            'reportType.in' => 'Invalid report type selected.',
            'reportDescription.required' => 'Please provide a description.',
            'reportDescription.min' => 'Description must be at least 10 characters.',
            'reportDescription.max' => 'Description cannot exceed 1000 characters.',
        ]);

        // Check if user has already reported this profile with pending status
        $existingReport = Report::where('user_id', auth()->id())
            ->where('profile_id', $this->profileid)
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            session()->flash('report_error', 'You have already submitted a report for this profile. Please wait for it to be reviewed.');
            return;
        }

        // Create the report
        try {
            Report::create([
                'user_id' => auth()->id(),
                'profile_id' => $this->profileid,
                'report_type' => $this->reportType,
                'description' => $this->reportDescription,
                'status' => 'pending',
            ]);

            // Clear the form
            $this->reset(['reportType', 'reportDescription']);

            session()->flash('report_message', 'Thank you for your report. Our team will review it shortly.');
            
            // Close modal using dispatch
            $this->dispatch('closeReportModal');
        } catch (\Exception $e) {
            session()->flash('report_error', 'An error occurred while submitting your report. Please try again.');
            \Log::error('Report submission error: ' . $e->getMessage());
        }
    }

}
