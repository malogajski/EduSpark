<?php

namespace App\Livewire;

use App\Models\Subject;
use App\Models\Quiz;
use App\Models\Attempt;
use App\Services\QuizCacheService;
use Livewire\Component;

class QuizStartPage extends Component
{
    public $step = 1; // 1: grade selection, 2: subject selection, 3: guest name if not authenticated
    public $selectedGrade = null;
    public $selectedSubject = null;
    public $guestName = '';
    public $subjects = [];
    public $availableQuizzes = [];

    public function mount()
    {
        $cacheService = app(QuizCacheService::class);
        $this->subjects = $cacheService->getSubjects();
    }

    public function selectGrade($grade)
    {
        $this->selectedGrade = $grade;
        $this->step = 2;

        // Load available quizzes for this grade from cache
        $cacheService = app(QuizCacheService::class);
        $allQuizzes = collect();

        foreach ($this->subjects as $subject) {
            $quizzes = $cacheService->getQuizzesByGradeAndSubject($grade, $subject->id);
            $allAgesQuizzes = $cacheService->getQuizzesByGradeAndSubject(0, $subject->id);

            if ($quizzes->count() > 0) {
                $allQuizzes = $allQuizzes->merge($quizzes);
            }
            if ($allAgesQuizzes->count() > 0) {
                $allQuizzes = $allQuizzes->merge($allAgesQuizzes);
            }
        }

        $this->availableQuizzes = $allQuizzes->groupBy('subject_id');
    }

    public function selectSubject($subjectId)
    {
        $this->selectedSubject = $subjectId;

        if (auth()->check()) {
            $this->startQuiz();
        } else {
            $this->step = 3; // Ask for guest name
        }
    }

    public function startQuiz()
    {
        if (!auth()->check() && empty($this->guestName)) {
            session()->flash('error', 'Name is required for guests.');
            return;
        }

        $quiz = Quiz::with('questions')
            ->where(function($query) {
                $query->where('grade', $this->selectedGrade)
                      ->orWhere('grade', 0);
            })
            ->where('subject_id', $this->selectedSubject)
            ->where('is_published', true)
            ->first();

        if (!$quiz) {
            session()->flash('error', 'Quiz not found.');
            return;
        }

        // Create attempt
        $attempt = \App\Models\Attempt::create([
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : $this->guestName,
            'quiz_id' => $quiz->id,
            'total' => $quiz->questions->count(),
            'started_at' => now(),
            'locale' => app()->getLocale(),
        ]);

        return redirect()->route('quiz.play', $quiz->id);
    }

    public function back()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function render()
    {
        return view('livewire.quiz-start-page')->layout('components.layouts.app', [
            'title' => 'Start Quiz - EduSpark'
        ]);
    }
}
