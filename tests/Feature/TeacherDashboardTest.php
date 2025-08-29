<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeacherDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_teacher_can_create_quiz()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::first();

        $this->actingAs($teacher);

        Livewire::test('teacher-dashboard')
            ->call('showCreateQuizForm')
            ->assertSet('showCreateQuiz', true)
            ->set('newQuiz.grade', 3)
            ->set('newQuiz.subject_id', $subject->id)
            ->set('newQuiz.title.sr', 'Test Quiz')
            ->set('newQuiz.description.sr', 'Test Description')
            ->call('createQuiz')
            ->assertSet('showCreateQuiz', false);

        $this->assertDatabaseHas('quizzes', [
            'grade' => 3,
            'subject_id' => $subject->id,
            'title' => json_encode([
                'sr' => 'Test Quiz',
                'en' => 'Test Quiz',
                'hu' => 'Test Quiz',
            ]),
            'is_published' => true,
        ]);
    }

    public function test_teacher_can_edit_quiz()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 2,
            'subject_id' => $subject->id,
            'title' => ['sr' => 'Original Title', 'en' => 'Original Title', 'hu' => 'Original Title'],
            'description' => ['sr' => 'Original Description', 'en' => 'Original Description', 'hu' => 'Original Description'],
        ]);

        $this->actingAs($teacher);

        Livewire::test('teacher-dashboard')
            ->call('editQuiz', $quiz->id)
            ->assertSet('editingQuiz', $quiz->id)
            ->assertSet('showCreateQuiz', true)
            ->assertSet('newQuiz.title.sr', 'Original Title')
            ->set('newQuiz.title.sr', 'Updated Title')
            ->set('newQuiz.description.sr', 'Updated Description')
            ->call('updateQuiz')
            ->assertSet('editingQuiz', null)
            ->assertSet('showCreateQuiz', false);

        // Refresh the quiz to get the updated data
        $quiz->refresh();
        
        $this->assertEquals('Updated Title', $quiz->title['sr']);
        $this->assertEquals('Updated Description', $quiz->description['sr']);
        $this->assertEquals('Original Title', $quiz->title['en']); // Should remain unchanged
        $this->assertEquals('Original Title', $quiz->title['hu']); // Should remain unchanged
    }

    public function test_teacher_can_delete_quiz()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $quiz = Quiz::factory()->create();

        $this->actingAs($teacher);

        Livewire::test('teacher-dashboard')
            ->call('deleteQuiz', $quiz->id);

        $this->assertDatabaseMissing('quizzes', [
            'id' => $quiz->id,
        ]);
    }

    public function test_teacher_can_cancel_edit()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $quiz = Quiz::factory()->create();

        $this->actingAs($teacher);

        Livewire::test('teacher-dashboard')
            ->call('editQuiz', $quiz->id)
            ->assertSet('editingQuiz', $quiz->id)
            ->call('cancelEdit')
            ->assertSet('editingQuiz', null)
            ->assertSet('showCreateQuiz', false);
    }

    public function test_teacher_dashboard_displays_quizzes()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'subject_id' => $subject->id,
            'title' => ['sr' => 'Test Quiz', 'en' => 'Test Quiz', 'hu' => 'Test Quiz'],
        ]);

        $this->actingAs($teacher);

        Livewire::test('teacher-dashboard')
            ->call('setTab', 'quizzes')
            ->assertSee('Test Quiz')
            ->assertSee($subject->getName())
            ->assertSee('Edit')
            ->assertSee('Delete');
    }

    public function test_non_teacher_cannot_access_teacher_dashboard()
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        Livewire::test('teacher-dashboard');
    }
}