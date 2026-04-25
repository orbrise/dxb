<?php

namespace App\Observers;

use App\Models\Question;
use App\Services\CacheVersion;

class QuestionObserver
{
    public function created(Question $question): void
    {
        $this->invalidate($question);
    }

    public function updated(Question $question): void
    {
        $this->invalidate($question);
    }

    public function deleted(Question $question): void
    {
        $this->invalidate($question);
    }

    protected function invalidate(Question $question): void
    {
        CacheVersion::bump(CacheVersion::profileScope($question->profile_id));
    }
}
