<?php

namespace App\Livewire;

use App\Models\Attempt;
use App\Models\Subject;
use App\Services\QuizCacheService;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDashboard extends Component
{
    use WithPagination;
    
    public $user;
    public $totalAttempts = 0;
    public $averageScore = 0;
    public $bestScore = 0;
    public $recentAttempts;
    public $selectedSubject = '';
    public $selectedGrade = '';
    public $searchTerm = '';
    
    public function mount()
    {
        $this->user = auth()->user();
        $this->loadStatistics();
        $this->loadRecentAttempts();
    }
    
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }
    
    public function updatingSelectedSubject()
    {
        $this->resetPage();
    }
    
    public function updatingSelectedGrade()
    {
        $this->resetPage();
    }
    
    private function loadStatistics()
    {
        $cacheService = app(QuizCacheService::class);
        $stats = $cacheService->getUserStats($this->user->id);
        
        $this->totalAttempts = $stats['total_attempts'];
        $this->averageScore = $stats['average_score'];
        $this->bestScore = $stats['best_score'];
    }
    
    private function loadRecentAttempts()
    {
        $this->recentAttempts = Attempt::with(['quiz.subject'])
            ->where('user_id', $this->user->id)
            ->whereNotNull('finished_at')
            ->orderBy('finished_at', 'desc')
            ->limit(5)
            ->get();
    }
    
    public function getFilteredAttemptsProperty()
    {
        $query = Attempt::with(['quiz.subject'])
            ->where('user_id', $this->user->id)
            ->whereNotNull('finished_at');
            
        if ($this->selectedSubject) {
            $query->whereHas('quiz.subject', function ($q) {
                $q->where('key', $this->selectedSubject);
            });
        }
        
        if ($this->selectedGrade) {
            $query->whereHas('quiz', function ($q) {
                $q->where('grade', $this->selectedGrade);
            });
        }
        
        if ($this->searchTerm) {
            $query->whereHas('quiz', function ($q) {
                $q->where('title->sr', 'LIKE', '%' . $this->searchTerm . '%')
                  ->orWhere('title->en', 'LIKE', '%' . $this->searchTerm . '%')
                  ->orWhere('title->hu', 'LIKE', '%' . $this->searchTerm . '%');
            });
        }
        
        return $query->orderBy('finished_at', 'desc')->paginate(10);
    }
    
    public function retakeQuiz($quizId)
    {
        return redirect()->route('quiz.start', ['quiz' => $quizId]);
    }
    
    public function viewResult($quizId, $attemptId)
    {
        return redirect()->route('quiz.result', [$quizId, $attemptId]);
    }
    
    public function startNewQuiz()
    {
        return redirect()->route('quiz.start');
    }
    
    public function render()
    {
        $cacheService = app(QuizCacheService::class);
        $subjects = $cacheService->getSubjects();
        $attempts = $this->getFilteredAttemptsProperty();
        
        return view('livewire.student-dashboard', [
            'subjects' => $subjects,
            'attempts' => $attempts,
        ])->layout('components.layouts.app', [
            'title' => 'Student Dashboard'
        ]);
    }
}