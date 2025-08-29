<?php

use App\Livewire\WelcomePage;
use App\Livewire\QuizStartPage;
use App\Livewire\QuizPlayPage;
use App\Livewire\QuizResultPage;
use App\Livewire\QuizEditor;
use App\Livewire\StudentDashboard;
use App\Livewire\TeacherDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomePage::class)->name('home');
Route::get('/start', QuizStartPage::class)->name('quiz.start');
Route::get('/quiz/{quiz}', QuizPlayPage::class)->middleware('quiz.access')->name('quiz.play');
Route::get('/quiz/{quiz}/result/{attempt}', QuizResultPage::class)->name('quiz.result');

Route::get('/teacher', TeacherDashboard::class)->middleware(['auth', 'role:teacher'])->name('teacher.dashboard');
Route::get('/teacher/quiz/{quizId}/edit', QuizEditor::class)->middleware(['auth', 'role:teacher'])->name('teacher.quiz.edit');
Route::get('/student', StudentDashboard::class)->middleware(['auth', 'role:student'])->name('student.dashboard');

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'teacher') {
        return redirect()->route('teacher.dashboard');
    }
    return redirect()->route('student.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
