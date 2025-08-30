<?php

namespace App\Livewire;

use App\Models\Subject;
use App\Models\Quiz;
use App\Models\Attempt;
use App\Services\QuizCacheService;
use Livewire\Component;

class QuizStartPage extends Component
{
    public $step = 1; // 1: grade selection, 2: subject selection, 3: quiz selection, 4: guest name if not authenticated
    public $selectedGrade = null;
    public $selectedSubject = null;
    public $selectedQuiz = null;
    public $guestName = '';
    public $subjects = [];
    public $availableQuizzes = [];
    public $quizChoices = [];

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

        // Check if there are multiple quizzes for this subject/grade combination
        if ($this->selectedGrade === 0) {
            // For "All Ages" (grade 0), only show grade 0 quizzes
            $quizzes = Quiz::where('grade', 0)
                ->where('subject_id', $this->selectedSubject)
                ->where('is_published', true)
                ->get();
        } else {
            // For specific grades, show both grade-specific and "All Ages" quizzes
            $quizzes = Quiz::where(function($query) {
                    $query->where('grade', $this->selectedGrade)
                          ->orWhere('grade', 0);
                })
                ->where('subject_id', $this->selectedSubject)
                ->where('is_published', true)
                ->get();
        }

        if ($quizzes->count() > 1) {
            // Multiple quizzes available, let user choose
            $this->quizChoices = $quizzes;
            $this->step = 3; // Quiz selection
        } else {
            // Only one quiz, proceed directly
            if ($quizzes->count() === 1) {
                $this->selectedQuiz = $quizzes->first()->id;
            }

            if (auth()->check()) {
                $this->startQuiz();
            } else {
                $this->step = 4; // Ask for guest name
            }
        }
    }

    public function selectQuiz($quizId)
    {
        $this->selectedQuiz = $quizId;

        if (auth()->check()) {
            $this->startQuiz();
        } else {
            $this->step = 4; // Ask for guest name
        }
    }

    public function startQuiz()
    {
        if (!auth()->check() && empty($this->guestName)) {
            session()->flash('error', 'Name is required for guests.');
            return;
        }

        if (!$this->selectedQuiz) {
            session()->flash('error', 'No quiz selected.');
            return;
        }

        $quiz = Quiz::with('questions')->find($this->selectedQuiz);

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

            // Reset selections when going back
            if ($this->step === 1) {
                $this->selectedGrade = null;
                $this->selectedSubject = null;
                $this->selectedQuiz = null;
                $this->availableQuizzes = [];
            } elseif ($this->step === 2) {
                $this->selectedSubject = null;
                $this->selectedQuiz = null;
                $this->availableQuizzes = [];
                $this->quizChoices = [];
            } elseif ($this->step === 3) {
                $this->selectedQuiz = null;
                $this->guestName = '';
            }
        }
    }

    public function render()
    {
        return view('livewire.quiz-start-page')->layout('components.layouts.app', [
            'title' => 'Start Quiz - EduSpark'
        ]);
    }
}
