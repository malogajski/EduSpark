<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Attempt;
use Livewire\Component;
use Livewire\WithPagination;


class TeacherDashboard extends Component
{
    use WithPagination;

    public $activeTab = 'overview';

    // Filters
    public $selectedGrade = '';
    public $selectedSubject = '';

    // Quiz creation
    public $showCreateQuiz = false;
    public $newQuiz = [
        'grade' => 1,
        'subject_id' => null,
        'title' => ['sr' => '', 'en' => '', 'hu' => ''],
        'description' => ['sr' => '', 'en' => '', 'hu' => ''],
    ];

    public $editingQuiz = null;

    public function mount()
    {
        // Check if user is teacher
        if (!auth()->check() || auth()->user()->role !== 'teacher') {
            abort(403, 'Access denied. Teachers only.');
        }

        // Set active tab from query parameter
        $this->activeTab = request()->query('tab', 'overview');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSelectedGrade()
    {
        $this->resetPage();
    }

    public function updatingSelectedSubject()
    {
        $this->resetPage();
    }

    public function showCreateQuizForm()
    {
        $this->showCreateQuiz = true;
        $this->newQuiz = [
            'grade' => 1,
            'subject_id' => null,
            'title' => ['sr' => '', 'en' => '', 'hu' => ''],
            'description' => ['sr' => '', 'en' => '', 'hu' => ''],
        ];
    }

    public function createQuiz()
    {
        $this->validate([
            'newQuiz.grade' => 'required|integer|min:1|max:8',
            'newQuiz.subject_id' => 'required|exists:subjects,id',
            'newQuiz.title.sr' => 'required|string|max:255',
            'newQuiz.description.sr' => 'nullable|string|max:500',
        ]);

        // Auto-fill other languages with Serbian if empty
        if (empty($this->newQuiz['title']['en'])) {
            $this->newQuiz['title']['en'] = $this->newQuiz['title']['sr'];
        }
        if (empty($this->newQuiz['title']['hu'])) {
            $this->newQuiz['title']['hu'] = $this->newQuiz['title']['sr'];
        }
        if (empty($this->newQuiz['description']['en'])) {
            $this->newQuiz['description']['en'] = $this->newQuiz['description']['sr'];
        }
        if (empty($this->newQuiz['description']['hu'])) {
            $this->newQuiz['description']['hu'] = $this->newQuiz['description']['sr'];
        }

        Quiz::create([
            'grade' => $this->newQuiz['grade'],
            'subject_id' => $this->newQuiz['subject_id'],
            'title' => $this->newQuiz['title'],
            'description' => $this->newQuiz['description'],
            'is_published' => true,
        ]);

        $this->cancelEdit();
        session()->flash('message', 'Quiz created successfully!');
    }

    public function editQuiz($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $this->editingQuiz = $quiz->id;
        $this->newQuiz = [
            'grade' => $quiz->grade,
            'subject_id' => $quiz->subject_id,
            'title' => $quiz->title,
            'description' => $quiz->description,
        ];
        $this->showCreateQuiz = true;
    }

    public function updateQuiz()
    {
        $this->validate([
            'newQuiz.grade' => 'required|integer|between:1,8',
            'newQuiz.subject_id' => 'required|exists:subjects,id',
            'newQuiz.title.sr' => 'required|string|max:255',
            'newQuiz.description.sr' => 'required|string',
        ]);

        // Auto-copy Serbian to other languages if they're empty
        if (empty($this->newQuiz['title']['en'])) {
            $this->newQuiz['title']['en'] = $this->newQuiz['title']['sr'];
        }
        if (empty($this->newQuiz['title']['hu'])) {
            $this->newQuiz['title']['hu'] = $this->newQuiz['title']['sr'];
        }
        if (empty($this->newQuiz['description']['en'])) {
            $this->newQuiz['description']['en'] = $this->newQuiz['description']['sr'];
        }
        if (empty($this->newQuiz['description']['hu'])) {
            $this->newQuiz['description']['hu'] = $this->newQuiz['description']['sr'];
        }

        $quiz = Quiz::findOrFail($this->editingQuiz);
        $quiz->update([
            'grade' => $this->newQuiz['grade'],
            'subject_id' => $this->newQuiz['subject_id'],
            'title' => $this->newQuiz['title'],
            'description' => $this->newQuiz['description'],
        ]);

        $this->cancelEdit();
        session()->flash('message', 'Quiz updated successfully!');
    }

    public function cancelEdit()
    {
        $this->editingQuiz = null;
        $this->showCreateQuiz = false;
        $this->newQuiz = [
            'grade' => 1,
            'subject_id' => null,
            'title' => ['sr' => '', 'en' => '', 'hu' => ''],
            'description' => ['sr' => '', 'en' => '', 'hu' => ''],
        ];
    }

    public function deleteQuiz($quizId)
    {
        Quiz::findOrFail($quizId)->delete();
        session()->flash('message', 'Quiz deleted successfully!');
    }

    public function render()
    {
        $data = [];

        switch ($this->activeTab) {
            case 'overview':
                $data['totalQuizzes'] = Quiz::count();
                $data['totalAttempts'] = Attempt::count();
                $data['avgScore'] = Attempt::whereNotNull('finished_at')->avg('score') ?? 0;
                $data['recentAttempts'] = Attempt::with(['quiz', 'user'])
                    ->whereNotNull('finished_at')
                    ->orderBy('finished_at', 'desc')
                    ->limit(5)
                    ->get();
                break;

            case 'quizzes':
                $query = Quiz::with('subject');

                if ($this->selectedGrade) {
                    $query->where('grade', $this->selectedGrade);
                }

                if ($this->selectedSubject) {
                    $query->where('subject_id', $this->selectedSubject);
                }

                $data['quizzes'] = $query->orderBy('grade')->orderBy('subject_id')->paginate(10);
                $data['subjects'] = Subject::all();
                break;

            case 'attempts':
                $data['attempts'] = Attempt::with(['quiz.subject', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
                break;
        }

        return view('livewire.teacher-dashboard', $data)->layout('components.layouts.app', [
            'title' => 'Teacher Dashboard - EduSpark'
        ]);
    }
}
