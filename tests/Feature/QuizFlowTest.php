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

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_access_welcome_page()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('EduSpark');
        $response->assertSee('Start Quiz');
    }

    public function test_guest_can_start_quiz_selection()
    {
        $response = $this->get('/start');
        
        $response->assertStatus(200);
        $response->assertSeeLivewire('quiz-start-page');
    }

    public function test_guest_quiz_flow_complete()
    {
        // Create test data
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'order' => 1,
        ]);
        
        $correctAnswer = Answer::factory()->create([
            'question_id' => $question->id,
            'is_correct' => true,
            'order' => 1,
        ]);
        
        Answer::factory()->create([
            'question_id' => $question->id,
            'is_correct' => false,
            'order' => 2,
        ]);

        // Start quiz selection
        Livewire::test('quiz-start-page')
            ->call('selectGrade', 1)
            ->assertSet('selectedGrade', 1)
            ->assertSet('step', 2)
            ->call('selectSubject', $subject->id)
            ->assertSet('selectedSubject', $subject->id)
            ->assertSet('step', 3)
            ->set('guestName', 'Test User')
            ->call('startQuiz')
            ->assertRedirect();

        // Verify attempt was created
        $attempt = Attempt::where('guest_name', 'Test User')->first();
        $this->assertNotNull($attempt);
        $this->assertEquals($quiz->id, $attempt->quiz_id);
        $this->assertNull($attempt->finished_at);
    }

    public function test_authenticated_student_can_take_quiz()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);

        $this->actingAs($student);

        Livewire::test('quiz-start-page')
            ->call('selectGrade', 1)
            ->call('selectSubject', $subject->id)
            ->assertRedirect();

        $attempt = Attempt::where('user_id', $student->id)->first();
        $this->assertNotNull($attempt);
        $this->assertEquals($quiz->id, $attempt->quiz_id);
    }

    public function test_quiz_play_displays_questions_correctly()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'order' => 1,
        ]);
        
        $answers = Answer::factory()->count(4)->create([
            'question_id' => $question->id,
        ]);

        $attempt = Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
        ]);

        $this->actingAs($student);

        Livewire::test('quiz-play-page', ['quiz' => $quiz->id])
            ->assertSee($question->getPrompt())
            ->assertSee($answers->first()->getText())
            ->assertSet('currentQuestionIndex', 0)
            ->assertSet('isAnswered', false);
    }

    public function test_quiz_answer_submission_works()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'order' => 1,
        ]);
        
        $correctAnswer = Answer::factory()->create([
            'question_id' => $question->id,
            'is_correct' => true,
        ]);

        $attempt = Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
        ]);

        $this->actingAs($student);

        Livewire::test('quiz-play-page', ['quiz' => $quiz->id])
            ->call('selectAnswer', $correctAnswer->id)
            ->assertSet('selectedAnswer', $correctAnswer->id)
            ->call('submitAnswer')
            ->assertSet('isAnswered', true)
            ->assertSet('showExplanation', true);

        // Verify attempt answer was recorded
        $attemptAnswer = AttemptAnswer::where('attempt_id', $attempt->id)->first();
        $this->assertNotNull($attemptAnswer);
        $this->assertEquals($correctAnswer->id, $attemptAnswer->answer_id);
        $this->assertTrue($attemptAnswer->is_correct);
    }

    public function test_quiz_completion_redirects_to_results()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'order' => 1,
        ]);
        
        $correctAnswer = Answer::factory()->create([
            'question_id' => $question->id,
            'is_correct' => true,
        ]);

        $attempt = Attempt::factory()->create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
        ]);

        $this->actingAs($student);

        Livewire::test('quiz-play-page', ['quiz' => $quiz->id])
            ->call('selectAnswer', $correctAnswer->id)
            ->call('submitAnswer')
            ->call('completeQuiz')
            ->assertRedirect(route('quiz.result', [$quiz->id, $attempt->id]));

        // Verify attempt is marked as finished
        $attempt->refresh();
        $this->assertNotNull($attempt->finished_at);
        $this->assertEquals(1, $attempt->score);
    }

    public function test_quiz_results_page_displays_correctly()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $question = Question::factory()->create([
            'quiz_id' => $quiz->id,
            'order' => 1,
        ]);
        
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

        AttemptAnswer::factory()->create([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer_id' => $correctAnswer->id,
            'is_correct' => true,
        ]);

        $this->actingAs($student);

        Livewire::test('quiz-result-page', ['quiz' => $quiz->id, 'attempt' => $attempt->id])
            ->assertSee('Congratulations!')
            ->assertSee('100%')
            ->assertSee($question->getPrompt())
            ->assertSee($correctAnswer->getText());
    }

    public function test_unpublished_quiz_returns_404()
    {
        $quiz = Quiz::factory()->create(['is_published' => false]);

        $response = $this->get('/quiz/' . $quiz->id);
        $response->assertStatus(404);
    }

    public function test_nonexistent_quiz_returns_404()
    {
        $response = $this->get('/quiz/999');
        $response->assertStatus(404);
    }
}