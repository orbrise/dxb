<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;
use Livewire\WithPagination;

class QuestionManagement extends Component
{
    use WithPagination;

    public $answer;
    public $questionId;
    public $search = '';

    public function render()
    {
        $questions = Question::query()
            ->with(['getuser', 'profile'])
            ->where(function($query) {
                $query->where('question', 'like', '%' . $this->search . '%')
                      ->orWhereHas('getuser', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('profile', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('livewire.question-management', compact('questions'));
    }

    public function submitAnswer()
    {
        

        $question = Question::find($this->questionId);
        $question->update([
            'answer' => $this->answer,
            'answer_status' => 1
        ]);
        $this->dispatch('closeModal');
        $this->reset(['answer', 'questionId']);
        session()->flash('success', 'Answer submitted successfully');
    }

    public function updatedSearch()
{
    $this->resetPage();
}




}
