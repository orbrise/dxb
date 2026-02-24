<?php

namespace App\Livewire\Optimized;

use Livewire\Component;
use App\Models\Question;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class OptimizedQuestionManagement extends Component
{
    use WithPagination;

    public $answer;
    public $questionId;
    public $search = '';
    public $filterByStatus = 'all'; // Add status filter
    
    protected $queryString = ['search', 'filterByStatus'];

    public function render()
    {
        $cacheKey = 'questions_' . md5($this->search . $this->filterByStatus . $this->getPage());
        
        $questions = Cache::remember($cacheKey, 60, function () { // 1 minute cache
            return Question::query()
                // Use select to limit columns fetched
                ->select(['id', 'question', 'answer', 'answer_status', 'user_id', 'profile_id', 'created_at'])
                // Eager load with limited columns
                ->with([
                    'getuser:id,name',
                    'profile:id,name'
                ])
                ->when($this->search, function (Builder $query) {
                    $query->where(function($q) {
                        $q->where('question', 'like', '%' . $this->search . '%')
                          ->orWhereHas('getuser', function($userQuery) {
                              $userQuery->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('profile', function($profileQuery) {
                              $profileQuery->where('name', 'like', '%' . $this->search . '%');
                          });
                    });
                })
                ->when($this->filterByStatus !== 'all', function (Builder $query) {
                    $status = $this->filterByStatus === 'answered' ? 1 : 0;
                    $query->where('answer_status', $status);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        });
            
        return view('livewire.optimized.question-management', compact('questions'));
    }

    public function submitAnswer()
    {
        $this->validate([
            'answer' => 'required|string|max:1000',
            'questionId' => 'required|exists:questions,id'
        ]);

        $question = Question::find($this->questionId);
        
        $question->update([
            'answer' => $this->answer,
            'answer_status' => 1
        ]);
        
        // Clear related caches
        Cache::forget('questions_*');
        
        $this->dispatch('closeModal');
        $this->reset(['answer', 'questionId']);
        session()->flash('success', 'Answer submitted successfully');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedFilterByStatus()
    {
        $this->resetPage();
    }

    protected function getPage()
    {
        return request()->get('page', 1);
    }
}
