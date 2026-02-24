<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Review;
use App\Models\UsersProfile;

class Reviews extends Component
{
    use WithPagination;

    public $selectedReview = null;
    public $reply = '';
    public $searchTerm = '';
    public $filterStatus = 'all';
    public $filterRating = 'all';
    public $reviewsList = [];

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'reply' => 'required|min:5|max:1000',
    ];

    public function mount()
    {
        // Ensure user is authenticated
        if (!auth()->check()) {
            return redirect()->route('sign-in');
        }

        // Check if user has any profiles
        $hasProfiles = UsersProfile::where('user_id', auth()->id())->exists();
        if (!$hasProfiles) {
            session()->flash('info', 'You need to create a profile first to receive reviews.');
        }
    }

    public function render()
    {
        // Get all profiles owned by the authenticated user
        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');

        // If no profiles, return empty data
        if ($userProfiles->isEmpty()) {
            return view('livewire.reviews', [
                'reviews' => collect(),
                'totalReviews' => 0,
                'unrepliedCount' => 0,
                'repliedCount' => 0,
                'avgRating' => 0,
                'activeCount' => 0,
                'rejectedCount' => 0,
                'pendingCount' => 0,
                'archivedCount' => 0
            ])->layout('components.layouts.app');
        }

        // Build query for reviews
        $query = Review::whereIn('profile_id', $userProfiles)
            ->with(['profile', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('review', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply rating filter
        if ($this->filterRating !== 'all') {
            $query->where('star', $this->filterRating);
        }

        // Apply reply status filter
        if ($this->filterStatus === 'unreplied') {
            $query->whereNull('reply');
        } elseif ($this->filterStatus === 'replied') {
            $query->whereNotNull('reply');
        }

        $reviews = $query->paginate(15);

        // Count statistics
        $totalReviews = Review::whereIn('profile_id', $userProfiles)->count();
        $unrepliedCount = Review::whereIn('profile_id', $userProfiles)
            ->whereNull('reply')
            ->count();
        $repliedCount = Review::whereIn('profile_id', $userProfiles)
            ->whereNotNull('reply')
            ->count();
        $avgRating = Review::whereIn('profile_id', $userProfiles)
            ->avg('star');

        // Get profile dashboard navigation counts
        $activeCount = UsersProfile::where('user_id', auth()->id())
            ->where('is_active', 1)
            ->count();
            
        $rejectedCount = UsersProfile::where('user_id', auth()->id())
            ->whereHas('rejectedVerification')
            ->count();
            
        $pendingCount = UsersProfile::where('user_id', auth()->id())
            ->where('is_active', 0)
            ->count();
            
        $archivedCount = UsersProfile::where('user_id', auth()->id())
            ->whereNotNull('archived_at')
            ->count();

        return view('livewire.reviews', compact(
            'reviews',
            'totalReviews',
            'unrepliedCount',
            'repliedCount',
            'avgRating',
            'activeCount',
            'rejectedCount',
            'pendingCount',
            'archivedCount'
        ))->layout('components.layouts.app');
    }

    public function selectReview($reviewId)
    {
        $this->selectedReview = Review::with(['profile', 'user'])->find($reviewId);
        $this->reply = $this->selectedReview->reply ?? '';
        
        // Reset validation when selecting a new review
        $this->resetValidation();
    }

    public function sendReply()
    {
        $this->validate();

        if (!$this->selectedReview) {
            session()->flash('error', 'No review selected.');
            return;
        }

        $this->selectedReview->update([
            'reply' => $this->reply,
            'replied_at' => now()
        ]);
        
        $this->selectedReview->refresh();
        
        // Keep the review selected to show the reply
        $this->reply = $this->selectedReview->reply;

        session()->flash('success', 'Reply posted successfully!');
    }

    public function deleteReview($reviewId)
    {
        $review = Review::find($reviewId);
        if ($review) {
            $review->delete();
            session()->flash('success', 'Review deleted successfully.');
            
            if ($this->selectedReview && $this->selectedReview->id === $reviewId) {
                $this->selectedReview = null;
                $this->reply = '';
            }
        }
    }

    public function closeModal()
    {
        $this->selectedReview = null;
        $this->reply = '';
        $this->resetValidation();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
        $this->selectedReview = null;
        $this->reply = '';
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
        $this->selectedReview = null;
        $this->reply = '';
    }

    public function updatingFilterRating()
    {
        $this->resetPage();
        $this->selectedReview = null;
        $this->reply = '';
    }
}
