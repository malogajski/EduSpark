<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Attempt;
use App\Models\AttemptAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // Subject Model Tests
    public function test_subject_has_correct_fillable_fields()
    {
        $subject = new Subject();
        $this->assertEquals(['key', 'name'], $subject->getFillable());
    }

    public function test_subject_name_is_cast_to_array()
    {
        $subject = Subject::factory()->create([
            'name' => ['sr' => 'Математика', 'en' => 'Mathematics']
        ]);

        $this->assertIsArray($subject->name);
        $this->assertEquals('Математика', $subject->name['sr']);
    }

    public function test_subject_get_name_method()
    {
        $subject = Subject::factory()->create([
            'name' => [
                'sr' => 'Математика',
                'en' => 'Mathematics',
                'hu' => 'Matematika'
            ]
        ]);

        app()->setLocale('sr');
        $this->assertEquals('Математика', $subject->getName());

        app()->setLocale('en');
        $this->assertEquals('Mathematics', $subject->getName());

        app()->setLocale('hu');
        $this->assertEquals('Matematika', $subject->getName());
    }

    public function test_subject_get_translation_fallback()
    {
        $subject = Subject::factory()->create([
            'name' => ['sr' => 'Математика'] // Only Serbian translation
        ]);

        app()->setLocale('en');
        $this->assertEquals('Математика', $subject->getName()); // Should fallback to Serbian
    }

    // Quiz Model Tests
    public function test_quiz_belongs_to_subject()
    {
        $subject = Subject::factory()->create();
        $quiz = Quiz::factory()->create(['subject_id' => $subject->id]);

        $this->assertInstanceOf(Subject::class, $quiz->subject);
        $this->assertEquals($subject->id, $quiz->subject->id);
    }

    public function test_quiz_has_many_questions()
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory()->count(3)->create(['quiz_id' => $quiz->id]);

        $this->assertCount(3, $quiz->questions);
        $this->assertInstanceOf(Question::class, $quiz->questions->first());
    }

    public function test_quiz_has_many_attempts()
    {
        $quiz = Quiz::factory()->create();
        $attempts = Attempt::factory()->count(2)->create(['quiz_id' => $quiz->id]);

        $this->assertCount(2, $quiz->attempts);
        $this->assertInstanceOf(Attempt::class, $quiz->attempts->first());
    }

    public function test_quiz_translation_methods()
    {
        $quiz = Quiz::factory()->create([
            'title' => [
                'sr' => 'Квиз математике',
                'en' => 'Math Quiz',
                'hu' => 'Matek kvíz'
            ],
            'description' => [
                'sr' => 'Опис квиза',
                'en' => 'Quiz description',
                'hu' => 'Kvíz leírás'
            ]
        ]);

        app()->setLocale('en');
        $this->assertEquals('Math Quiz', $quiz->getTitle());
        $this->assertEquals('Quiz description', $quiz->getDescription());
    }

    // Question Model Tests
    public function test_question_belongs_to_quiz()
    {
        $quiz = Quiz::factory()->create();
        $question = Question::factory()->create(['quiz_id' => $quiz->id]);

        $this->assertInstanceOf(Quiz::class, $question->quiz);
        $this->assertEquals($quiz->id, $question->quiz->id);
    }

    public function test_question_has_many_answers()
    {
        $question = Question::factory()->create();
        $answers = Answer::factory()->count(4)->create(['question_id' => $question->id]);

        $this->assertCount(4, $question->answers);
        $this->assertInstanceOf(Answer::class, $question->answers->first());
    }

    public function test_question_translation_methods()
    {
        $question = Question::factory()->create([
            'prompt' => [
                'sr' => 'Колико је 2+2?',
                'en' => 'What is 2+2?',
                'hu' => 'Mennyi 2+2?'
            ],
            'explanation' => [
                'sr' => 'Објашњење',
                'en' => 'Explanation',
                'hu' => 'Magyarázat'
            ]
        ]);

        app()->setLocale('en');
        $this->assertEquals('What is 2+2?', $question->getPrompt());
        $this->assertEquals('Explanation', $question->getExplanation());
    }

    // Answer Model Tests
    public function test_answer_belongs_to_question()
    {
        $question = Question::factory()->create();
        $answer = Answer::factory()->create(['question_id' => $question->id]);

        $this->assertInstanceOf(Question::class, $answer->question);
        $this->assertEquals($question->id, $answer->question->id);
    }

    public function test_answer_translation_method()
    {
        $answer = Answer::factory()->create([
            'text' => [
                'sr' => '4',
                'en' => '4',
                'hu' => '4'
            ]
        ]);

        $this->assertEquals('4', $answer->getText());
    }

    // Attempt Model Tests
    public function test_attempt_belongs_to_user()
    {
        $user = User::factory()->create();
        $attempt = Attempt::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $attempt->user);
        $this->assertEquals($user->id, $attempt->user->id);
    }

    public function test_attempt_belongs_to_quiz()
    {
        $quiz = Quiz::factory()->create();
        $attempt = Attempt::factory()->create(['quiz_id' => $quiz->id]);

        $this->assertInstanceOf(Quiz::class, $attempt->quiz);
        $this->assertEquals($quiz->id, $attempt->quiz->id);
    }

    public function test_attempt_has_many_attempt_answers()
    {
        $attempt = Attempt::factory()->create();
        $attemptAnswers = AttemptAnswer::factory()->count(3)->create(['attempt_id' => $attempt->id]);

        $this->assertCount(3, $attempt->attemptAnswers);
        $this->assertInstanceOf(AttemptAnswer::class, $attempt->attemptAnswers->first());
    }

    public function test_attempt_get_player_name_method()
    {
        // Test with authenticated user
        $user = User::factory()->create(['name' => 'John Doe']);
        $attempt = Attempt::factory()->create(['user_id' => $user->id]);
        $this->assertEquals('John Doe', $attempt->getPlayerName());

        // Test with guest user
        $guestAttempt = Attempt::factory()->create([
            'user_id' => null,
            'guest_name' => 'Guest Player'
        ]);
        $this->assertEquals('Guest Player', $guestAttempt->getPlayerName());
    }

    // AttemptAnswer Model Tests
    public function test_attempt_answer_belongs_to_attempt()
    {
        $attempt = Attempt::factory()->create();
        $attemptAnswer = AttemptAnswer::factory()->create(['attempt_id' => $attempt->id]);

        $this->assertInstanceOf(Attempt::class, $attemptAnswer->attempt);
        $this->assertEquals($attempt->id, $attemptAnswer->attempt->id);
    }

    public function test_attempt_answer_belongs_to_question()
    {
        $question = Question::factory()->create();
        $attemptAnswer = AttemptAnswer::factory()->create(['question_id' => $question->id]);

        $this->assertInstanceOf(Question::class, $attemptAnswer->question);
        $this->assertEquals($question->id, $attemptAnswer->question->id);
    }

    public function test_attempt_answer_belongs_to_answer()
    {
        $answer = Answer::factory()->create();
        $attemptAnswer = AttemptAnswer::factory()->create(['answer_id' => $answer->id]);

        $this->assertInstanceOf(Answer::class, $attemptAnswer->answer);
        $this->assertEquals($answer->id, $attemptAnswer->answer->id);
    }

    // User Model Tests
    public function test_user_has_many_attempts()
    {
        $user = User::factory()->create();
        $attempts = Attempt::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->attempts);
        $this->assertInstanceOf(Attempt::class, $user->attempts->first());
    }

    public function test_user_role_enum()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);

        $this->assertEquals('teacher', $teacher->role);
        $this->assertEquals('student', $student->role);
    }
}