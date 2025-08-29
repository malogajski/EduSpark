<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\AttemptAnswer;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class QuizPlayPage extends Component
{
    public Quiz $quiz;
    public Attempt $attempt;
    public $questions = [];
    public $currentQuestionIndex = 0;
    public $selectedAnswer = null;
    public $isAnswered = false;
    public $showExplanation = false;
    public $timeRemaining = null;
    public $startTime;

    public function mount($quiz)
    {
        try {
            // Handle both Quiz model and ID
            if ($quiz instanceof Quiz) {
                $this->quiz = $quiz->load(['questions.answers', 'subject']);
            } else {
                $this->quiz = Quiz::with(['questions.answers', 'subject'])->findOrFail($quiz);
            }

            if (!$this->quiz->is_published) {
                abort(404, 'Quiz not found or not published.');
            }

            // Find the most recent unfinished attempt or create new one
            $this->attempt = Attempt::where('quiz_id', $this->quiz->id)
                ->where(function($query) {
                    if (auth()->check()) {
                        $query->where('user_id', auth()->id());
                    } else {
                        $query->whereNotNull('guest_name');
                    }
                })
                ->whereNull('finished_at')
                ->latest()
                ->first();

            if (!$this->attempt) {
                // If no attempt exists, redirect back to start
                session()->flash('error', 'No active quiz attempt found. Please start the quiz again.');
                return redirect()->route('quiz.start');
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Quiz not found.');
            return redirect()->route('quiz.start');
        } catch (\Exception $e) {
            \Log::error('Error loading quiz: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while loading the quiz. Please try again.');
            return redirect()->route('quiz.start');
        }

        $this->questions = $this->quiz->questions()->with('answers')->orderBy('order')->get();
        $this->startTime = now();

        // Find current question based on answered questions
        $answeredQuestions = AttemptAnswer::where('attempt_id', $this->attempt->id)->count();
        $this->currentQuestionIndex = $answeredQuestions;

        // Check if quiz is already completed
        if ($this->currentQuestionIndex >= $this->questions->count()) {
            return $this->completeQuiz();
        }

        // Check if current question is already answered
        $currentQuestion = $this->questions[$this->currentQuestionIndex];
        $existingAnswer = AttemptAnswer::where('attempt_id', $this->attempt->id)
            ->where('question_id', $currentQuestion->id)
            ->first();

        if ($existingAnswer) {
            $this->selectedAnswer = $existingAnswer->answer_id;
            $this->isAnswered = true;
            $this->showExplanation = true;
        }
    }

    public function selectAnswer($answerId)
    {
        if ($this->isAnswered) {
            return;
        }

        $this->selectedAnswer = $answerId;
    }

    public function submitAnswer()
    {
        if ($this->isAnswered || !$this->selectedAnswer) {
            return;
        }

        try {
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $selectedAnswerModel = $currentQuestion->answers()->find($this->selectedAnswer);

            if (!$selectedAnswerModel) {
                session()->flash('error', 'Invalid answer selected. Please try again.');
                return;
            }

            DB::beginTransaction();

            // Save the attempt answer
            AttemptAnswer::create([
                'attempt_id' => $this->attempt->id,
                'question_id' => $currentQuestion->id,
                'answer_id' => $this->selectedAnswer,
                'is_correct' => $selectedAnswerModel->is_correct,
            ]);

            // Update attempt score
            $this->updateAttemptScore();

            DB::commit();

            $this->isAnswered = true;
            $this->showExplanation = true;

            // Auto advance after 3 seconds if not the last question
            if ($this->currentQuestionIndex < $this->questions->count() - 1) {
                $this->dispatch('auto-advance');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting answer: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting your answer. Please try again.');
        }
    }

    public function nextQuestion()
    {
        if (!$this->isAnswered) {
            return;
        }

        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
            $this->selectedAnswer = null;
            $this->isAnswered = false;
            $this->showExplanation = false;

            // Check if the new question is already answered
            $currentQuestion = $this->questions[$this->currentQuestionIndex];
            $existingAnswer = AttemptAnswer::where('attempt_id', $this->attempt->id)
                ->where('question_id', $currentQuestion->id)
                ->first();

            if ($existingAnswer) {
                $this->selectedAnswer = $existingAnswer->answer_id;
                $this->isAnswered = true;
                $this->showExplanation = true;
            }
        } else {
            $this->completeQuiz();
        }
    }

    private function updateAttemptScore()
    {
        $correctAnswers = AttemptAnswer::where('attempt_id', $this->attempt->id)
            ->where('is_correct', true)
            ->count();

        $this->attempt->update(['score' => $correctAnswers]);
    }

    public function completeQuiz()
    {
        // Final score calculation
        $this->updateAttemptScore();

        $this->attempt->update([
            'finished_at' => now(),
        ]);

        return redirect()->route('quiz.result', [$this->quiz->id, $this->attempt->id]);
    }

    public function getCurrentQuestion()
    {
        if ($this->currentQuestionIndex < $this->questions->count()) {
            return $this->questions[$this->currentQuestionIndex];
        }
        return null;
    }

    public function getProgress()
    {
        return round(($this->currentQuestionIndex / $this->questions->count()) * 100);
    }

    public function render()
    {
        $currentQuestion = $this->getCurrentQuestion();

        if (!$currentQuestion) {
            return $this->completeQuiz();
        }

        return view('livewire.quiz-play-page', [
            'currentQuestion' => $currentQuestion,
            'progress' => $this->getProgress(),
            'questionNumber' => $this->currentQuestionIndex + 1,
            'totalQuestions' => $this->questions->count(),
        ])->layout('components.layouts.app', [
            'title' => 'Playing Quiz - ' . $this->quiz->getTitle()
        ]);
    }
}
