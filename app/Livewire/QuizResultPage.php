<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Livewire\Component;

class QuizResultPage extends Component
{
    public Quiz $quiz;
    public Attempt $attempt;
    public $answers = [];
    public $percentage;
    public $passed;

    public function mount(Quiz $quiz, Attempt $attempt)
    {
        $this->quiz = $quiz->load(['questions.answers', 'subject']);
        $this->attempt = $attempt->load(['attemptAnswers.question', 'attemptAnswers.answer']);

        // Verify the attempt belongs to the quiz
        if ($this->attempt->quiz_id !== $this->quiz->id) {
            abort(404, 'Invalid attempt for this quiz.');
        }

        // Verify the attempt belongs to current user
        if (auth()->check()) {
            if ($this->attempt->user_id !== auth()->id()) {
                abort(403, 'You do not have permission to view this result.');
            }
        } else {
            // For guests, we'll allow viewing any finished attempt (no strict security)
            if (!$this->attempt->finished_at) {
                abort(404, 'Quiz attempt not completed yet.');
            }
        }

        $this->loadAnswerDetails();
        $this->calculateResults();
    }

    private function loadAnswerDetails()
    {
        $this->answers = $this->attempt->attemptAnswers->map(function ($attemptAnswer) {
            $question = $attemptAnswer->question;
            $selectedAnswer = $attemptAnswer->answer;
            $correctAnswer = $question->answers()->where('is_correct', true)->first();

            return [
                'question' => $question,
                'selected_answer' => $selectedAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $attemptAnswer->is_correct,
                'question_number' => $question->order ?? 0,
            ];
        })->sortBy('question_number')->values();
    }

    private function calculateResults()
    {
        $totalQuestions = $this->quiz->questions->count();
        $correctAnswers = $this->attempt->score;

        $this->percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 1) : 0;
        $this->passed = $this->percentage >= 70; // 70% passing threshold
    }

    public function startNewQuiz()
    {
        return redirect()->route('quiz.start');
    }

    public function viewDashboard()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.quiz-result-page')->layout('components.layouts.app', [
            'title' => 'Quiz Results - ' . $this->quiz->getTitle()
        ]);
    }
}
