<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Livewire\Component;

class QuizEditor extends Component
{
    public Quiz $quiz;
    public $questions = [];
    public $showAddQuestion = false;
    public $editingQuestion = null;
    public $newQuestion = [
        'prompt' => ['sr' => '', 'en' => '', 'hu' => ''],
        'explanation' => ['sr' => '', 'en' => '', 'hu' => ''],
        'question_type' => 'single_choice',
        'answers' => []
    ];

    public function mount($quizId)
    {
        $this->quiz = Quiz::with(['questions.answers' => function ($query) {
            $query->orderBy('order');
        }])->findOrFail($quizId);
        
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $this->questions = $this->quiz->questions()->with('answers')->orderBy('order')->get()->toArray();
    }

    public function showAddQuestionForm()
    {
        $this->showAddQuestion = true;
        $this->editingQuestion = null;
        $this->resetNewQuestion();
        $this->addEmptyAnswers();
    }

    public function editQuestion($questionId)
    {
        $question = Question::with('answers')->findOrFail($questionId);
        $this->editingQuestion = $questionId;
        $this->showAddQuestion = true;
        
        $this->newQuestion = [
            'prompt' => $question->prompt,
            'explanation' => $question->explanation ?? ['sr' => '', 'en' => '', 'hu' => ''],
            'question_type' => $question->question_type,
            'answers' => $question->answers->map(function ($answer) {
                return [
                    'id' => $answer->id,
                    'text' => $answer->text,
                    'is_correct' => $answer->is_correct,
                    'order' => $answer->order
                ];
            })->toArray()
        ];
        
        // Ensure we have minimum answers based on question type
        $this->ensureMinimumAnswers();
    }

    public function updatedNewQuestionQuestionType()
    {
        $this->addEmptyAnswers();
    }

    private function addEmptyAnswers()
    {
        $minAnswers = match ($this->newQuestion['question_type']) {
            'true_false' => 2,
            'single_choice' => 4,
            'multiple_choice' => 4,
            default => 4
        };

        // If true/false, set predefined answers
        if ($this->newQuestion['question_type'] === 'true_false') {
            $this->newQuestion['answers'] = [
                [
                    'id' => null,
                    'text' => ['sr' => 'Тачно', 'en' => 'True', 'hu' => 'Igaz'],
                    'is_correct' => false,
                    'order' => 1
                ],
                [
                    'id' => null,
                    'text' => ['sr' => 'Нетачно', 'en' => 'False', 'hu' => 'Hamis'],
                    'is_correct' => false,
                    'order' => 2
                ]
            ];
        } else {
            // For other types, add empty answers if needed
            while (count($this->newQuestion['answers']) < $minAnswers) {
                $this->newQuestion['answers'][] = [
                    'id' => null,
                    'text' => ['sr' => '', 'en' => '', 'hu' => ''],
                    'is_correct' => false,
                    'order' => count($this->newQuestion['answers']) + 1
                ];
            }
        }
    }

    private function ensureMinimumAnswers()
    {
        $currentAnswers = count($this->newQuestion['answers']);
        $minAnswers = match ($this->newQuestion['question_type']) {
            'true_false' => 2,
            'single_choice' => 4,
            'multiple_choice' => 4,
            default => 4
        };

        while ($currentAnswers < $minAnswers) {
            $this->newQuestion['answers'][] = [
                'id' => null,
                'text' => ['sr' => '', 'en' => '', 'hu' => ''],
                'is_correct' => false,
                'order' => $currentAnswers + 1
            ];
            $currentAnswers++;
        }
    }

    public function addAnswer()
    {
        $this->newQuestion['answers'][] = [
            'id' => null,
            'text' => ['sr' => '', 'en' => '', 'hu' => ''],
            'is_correct' => false,
            'order' => count($this->newQuestion['answers']) + 1
        ];
    }

    public function removeAnswer($index)
    {
        if (count($this->newQuestion['answers']) > 2) {
            unset($this->newQuestion['answers'][$index]);
            $this->newQuestion['answers'] = array_values($this->newQuestion['answers']);
            
            // Reorder
            foreach ($this->newQuestion['answers'] as $i => &$answer) {
                $answer['order'] = $i + 1;
            }
        }
    }

    public function saveQuestion()
    {
        $this->validate([
            'newQuestion.prompt.sr' => 'required|string',
            'newQuestion.question_type' => 'required|in:single_choice,multiple_choice,true_false',
            'newQuestion.answers' => 'required|array|min:2',
            'newQuestion.answers.*.text.sr' => 'required|string',
        ]);

        // Auto-copy Serbian to other languages if empty
        if (empty($this->newQuestion['prompt']['en'])) {
            $this->newQuestion['prompt']['en'] = $this->newQuestion['prompt']['sr'];
        }
        if (empty($this->newQuestion['prompt']['hu'])) {
            $this->newQuestion['prompt']['hu'] = $this->newQuestion['prompt']['sr'];
        }

        // Validate correct answers
        $correctAnswers = collect($this->newQuestion['answers'])->filter(fn($a) => $a['is_correct'])->count();
        
        if ($this->newQuestion['question_type'] === 'single_choice' || $this->newQuestion['question_type'] === 'true_false') {
            if ($correctAnswers !== 1) {
                session()->flash('error', 'Single choice and True/False questions must have exactly one correct answer.');
                return;
            }
        } elseif ($this->newQuestion['question_type'] === 'multiple_choice') {
            if ($correctAnswers < 1) {
                session()->flash('error', 'Multiple choice questions must have at least one correct answer.');
                return;
            }
        }

        if ($this->editingQuestion) {
            $this->updateQuestion();
        } else {
            $this->createQuestion();
        }
    }

    private function createQuestion()
    {
        $question = Question::create([
            'quiz_id' => $this->quiz->id,
            'prompt' => $this->newQuestion['prompt'],
            'explanation' => empty($this->newQuestion['explanation']['sr']) ? null : $this->newQuestion['explanation'],
            'question_type' => $this->newQuestion['question_type'],
            'order' => $this->quiz->questions()->count() + 1,
        ]);

        $this->createAnswers($question);
        $this->cancelQuestion();
        $this->loadQuestions();
        session()->flash('message', 'Question created successfully!');
    }

    private function updateQuestion()
    {
        $question = Question::findOrFail($this->editingQuestion);
        $question->update([
            'prompt' => $this->newQuestion['prompt'],
            'explanation' => empty($this->newQuestion['explanation']['sr']) ? null : $this->newQuestion['explanation'],
            'question_type' => $this->newQuestion['question_type'],
        ]);

        // Delete existing answers and create new ones
        $question->answers()->delete();
        $this->createAnswers($question);
        
        $this->cancelQuestion();
        $this->loadQuestions();
        session()->flash('message', 'Question updated successfully!');
    }

    private function createAnswers($question)
    {
        foreach ($this->newQuestion['answers'] as $answerData) {
            // Auto-copy Serbian to other languages if empty
            $text = $answerData['text'];
            if (empty($text['en'])) $text['en'] = $text['sr'];
            if (empty($text['hu'])) $text['hu'] = $text['sr'];

            Answer::create([
                'question_id' => $question->id,
                'text' => $text,
                'is_correct' => $answerData['is_correct'],
                'order' => $answerData['order'],
            ]);
        }
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);
        $question->answers()->delete();
        $question->delete();
        
        $this->loadQuestions();
        session()->flash('message', 'Question deleted successfully!');
    }

    public function cancelQuestion()
    {
        $this->showAddQuestion = false;
        $this->editingQuestion = null;
        $this->resetNewQuestion();
    }

    private function resetNewQuestion()
    {
        $this->newQuestion = [
            'prompt' => ['sr' => '', 'en' => '', 'hu' => ''],
            'explanation' => ['sr' => '', 'en' => '', 'hu' => ''],
            'question_type' => 'single_choice',
            'answers' => []
        ];
    }

    public function render()
    {
        return view('livewire.quiz-editor')->layout('components.layouts.app', [
            'title' => 'Edit Quiz - ' . $this->quiz->getTitle()
        ]);
    }
}