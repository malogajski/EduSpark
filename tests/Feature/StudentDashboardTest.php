<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_student_dashboard_displays_statistics()
    {
        $student = User::factory()->create(['role' => 'student']);
        
        // Create test quiz and attempt
        $quiz = Quiz::factory()->create(['grade' => 1, 'is_published' => true]);
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);
        $correctAnswer = Answer::factory()->create([
            'question_id' => $question->id,
            'is_correct' => true,
        ]);
        
        $attempt = Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 1,
            'finished_at' => now(),
        ]);

        $this->actingAs($student);

        Livewire::test('student-dashboard')
            ->assertSet('totalAttempts', 1)
            ->assertSet('averageScore', 100.0)
            ->assertSet('bestScore', 100.0);
    }

    public function test_student_dashboard_displays_recent_attempts()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::first();
        
        // Create test quiz and attempt
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $attempt = Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 5,
            'finished_at' => now(),
        ]);

        $this->actingAs($student);

        Livewire::test('student-dashboard')
            ->assertSee($quiz->getTitle())
            ->assertSee('5/')
            ->assertSee($subject->getName());
    }

    public function test_student_dashboard_filters_work()
    {
        $student = User::factory()->create(['role' => 'student']);
        $mathSubject = Subject::where('key', 'math')->first();
        $serbianSubject = Subject::where('key', 'serbian')->first();
        
        // Create quizzes for different subjects and grades
        $mathQuiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $mathSubject->id,
            'is_published' => true,
        ]);
        
        $serbianQuiz = Quiz::factory()->create([
            'grade' => 2,
            'subject_id' => $serbianSubject->id,
            'is_published' => true,
        ]);
        
        Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $mathQuiz->id,
            'finished_at' => now(),
        ]);
        
        Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $serbianQuiz->id,
            'finished_at' => now(),
        ]);

        $this->actingAs($student);

        // Test subject filter
        Livewire::test('student-dashboard')
            ->set('selectedSubject', 'math')
            ->assertSee($mathQuiz->getTitle())
            ->assertDontSee($serbianQuiz->getTitle());

        // Test grade filter
        Livewire::test('student-dashboard')
            ->set('selectedGrade', '1')
            ->assertSee($mathQuiz->getTitle())
            ->assertDontSee($serbianQuiz->getTitle());

        // Test search filter
        Livewire::test('student-dashboard')
            ->set('searchTerm', 'Math')
            ->assertSee($mathQuiz->getTitle())
            ->assertDontSee($serbianQuiz->getTitle());
    }

    public function test_student_can_retake_quiz()
    {
        $student = User::factory()->create(['role' => 'student']);
        $quiz = Quiz::factory()->create(['is_published' => true]);
        
        Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'finished_at' => now(),
        ]);

        $this->actingAs($student);

        Livewire::test('student-dashboard')
            ->call('retakeQuiz', $quiz->id)
            ->assertRedirect(route('quiz.start', ['quiz' => $quiz->id]));
    }

    public function test_student_can_view_results()
    {
        $student = User::factory()->create(['role' => 'student']);
        $quiz = Quiz::factory()->create(['is_published' => true]);
        
        $attempt = Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'finished_at' => now(),
        ]);

        $this->actingAs($student);

        Livewire::test('student-dashboard')
            ->call('viewResult', $quiz->id, $attempt->id)
            ->assertRedirect(route('quiz.result', [$quiz->id, $attempt->id]));
    }

    public function test_student_dashboard_pagination_works()
    {
        $student = User::factory()->create(['role' => 'student']);
        $quiz = Quiz::factory()->create(['is_published' => true]);
        
        // Create 15 attempts to test pagination
        Attempt::factory()->count(15)->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'finished_at' => now(),
        ]);

        $this->actingAs($student);

        $component = Livewire::test('student-dashboard');
        
        // Should show 10 items per page by default
        $this->assertEquals(10, $component->get('attempts')->perPage());
        $this->assertEquals(15, $component->get('attempts')->total());
    }

    public function test_empty_dashboard_shows_appropriate_message()
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student);

        Livewire::test('student-dashboard')
            ->assertSet('totalAttempts', 0)
            ->assertSee('No quiz attempts found')
            ->assertSee('Take Your First Quiz');
    }
}