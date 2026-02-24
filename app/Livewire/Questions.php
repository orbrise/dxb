<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Question;
use App\Models\UsersProfile;

class Questions extends Component
{
    use WithPagination;

    public $selectedQuestion = null;
    public $answer = '';
    public $searchTerm = '';
    public $filterStatus = 'all';
    public $questionsList = [];

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'answer' => 'required|min:5|max:1000',
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
            session()->flash('info', 'You need to create a profile first to receive questions.');
        }
    }

    public function render()
    {
        // Get all profiles owned by the authenticated user
        $userProfiles = UsersProfile::where('user_id', auth()->id())->pluck('id');

        // If no profiles, return empty data
        if ($userProfiles->isEmpty()) {
            return view('livewire.questions', [
                'questions' => collect(),
                'totalQuestions' => 0,
                'unansweredCount' => 0,
                'answeredCount' => 0,
                'activeCount' => 0,
                'rejectedCount' => 0,
                'pendingCount' => 0,
                'archivedCount' => 0
            ])->layout('components.layouts.app');
        }

        // Build query for questions
        $query = Question::whereIn('profile_id', $userProfiles)
            ->with(['profile', 'askedBy'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('question', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply status filter
        if ($this->filterStatus === 'unanswered') {
            $query->whereNull('answer');
        } elseif ($this->filterStatus === 'answered') {
            $query->whereNotNull('answer');
        }

        $questions = $query->paginate(15);

        // Count statistics
        $totalQuestions = Question::whereIn('profile_id', $userProfiles)->count();
        $unansweredCount = Question::whereIn('profile_id', $userProfiles)
            ->whereNull('answer')
            ->count();
        $answeredCount = Question::whereIn('profile_id', $userProfiles)
            ->whereNotNull('answer')
            ->count();

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

        return view('livewire.questions', compact(
            'questions',
            'totalQuestions',
            'unansweredCount',
            'answeredCount',
            'activeCount',
            'rejectedCount',
            'pendingCount',
            'archivedCount'
        ))->layout('components.layouts.app');
    }

    public function selectQuestion($questionId)
    {
        $this->selectedQuestion = Question::with(['profile', 'askedBy'])->find($questionId);
        $this->answer = $this->selectedQuestion->answer ?? '';
        
        // Reset validation when selecting a new question
        $this->resetValidation();
    }

    public function sendAnswer()
    {
        $this->validate();

        if (!$this->selectedQuestion) {
            session()->flash('error', 'No question selected.');
            return;
        }

        $this->selectedQuestion->update([
            'answer' => $this->answer,
            'answered_at' => now()
        ]);
        
        $this->selectedQuestion->refresh();
        
        // Keep the question selected to show the answer
        $this->answer = $this->selectedQuestion->answer;

        session()->flash('success', 'Answer posted successfully!');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::find($questionId);
        if ($question) {
            $question->delete();
            session()->flash('success', 'Question deleted successfully.');
            
            if ($this->selectedQuestion && $this->selectedQuestion->id === $questionId) {
                $this->selectedQuestion = null;
                $this->answer = '';
            }
        }
    }

    public function closeModal()
    {
        $this->selectedQuestion = null;
        $this->answer = '';
        $this->resetValidation();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
        $this->selectedQuestion = null;
        $this->answer = '';
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
        $this->selectedQuestion = null;
        $this->answer = '';
    }
}
