<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Question;
use App\Models\Attempt;
use App\Services\QuizCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QuizCacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->cacheService = app(QuizCacheService::class);
        Cache::flush(); // Clear cache before each test
    }

    public function test_get_subjects_caches_subjects()
    {
        // First call should hit the database
        $subjects1 = $this->cacheService->getSubjects();
        
        // Second call should hit the cache
        $subjects2 = $this->cacheService->getSubjects();
        
        $this->assertEquals($subjects1->count(), $subjects2->count());
        $this->assertTrue(Cache::has('subjects:all'));
    }

    public function test_get_quiz_caches_quiz_data()
    {
        $quiz = Quiz::factory()->create(['is_published' => true]);
        
        // First call should hit the database
        $cachedQuiz1 = $this->cacheService->getQuiz($quiz->id, ['subject']);
        
        // Second call should hit the cache
        $cachedQuiz2 = $this->cacheService->getQuiz($quiz->id, ['subject']);
        
        $this->assertEquals($quiz->id, $cachedQuiz1->id);
        $this->assertEquals($quiz->id, $cachedQuiz2->id);
        $this->assertNotNull($cachedQuiz1->subject);
    }

    public function test_get_quizzes_by_grade_and_subject_caches_data()
    {
        $subject = Subject::first();
        $quiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        // First call should hit the database
        $quizzes1 = $this->cacheService->getQuizzesByGradeAndSubject(1, $subject->id);
        
        // Second call should hit the cache
        $quizzes2 = $this->cacheService->getQuizzesByGradeAndSubject(1, $subject->id);
        
        $this->assertEquals(1, $quizzes1->count());
        $this->assertEquals(1, $quizzes2->count());
        $this->assertEquals($quiz->id, $quizzes1->first()->id);
    }

    public function test_get_user_stats_caches_statistics()
    {
        $user = User::factory()->create(['role' => 'student']);
        $quiz = Quiz::factory()->create(['is_published' => true]);
        
        // Add questions to calculate score properly
        $questions = Question::factory()->count(3)->create(['quiz_id' => $quiz->id]);
        
        $attempt = Attempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 2, // 2 out of 3 correct
            'finished_at' => now(),
        ]);
        
        // First call should calculate from database
        $stats1 = $this->cacheService->getUserStats($user->id);
        
        // Second call should hit the cache
        $stats2 = $this->cacheService->getUserStats($user->id);
        
        $this->assertEquals(1, $stats1['total_attempts']);
        $this->assertEquals(1, $stats2['total_attempts']);
        $this->assertEquals(66.7, $stats1['average_score']); // 2/3 * 100 = 66.7%
        $this->assertEquals(66.7, $stats2['average_score']);
    }

    public function test_get_user_stats_with_no_attempts()
    {
        $user = User::factory()->create(['role' => 'student']);
        
        $stats = $this->cacheService->getUserStats($user->id);
        
        $this->assertEquals(0, $stats['total_attempts']);
        $this->assertEquals(0, $stats['average_score']);
        $this->assertEquals(0, $stats['best_score']);
    }

    public function test_clear_quiz_cache_removes_related_keys()
    {
        $quiz = Quiz::factory()->create(['is_published' => true]);
        
        // Populate cache
        $this->cacheService->getQuiz($quiz->id);
        $this->assertTrue(Cache::has('quiz:' . $quiz->id . ':'));
        
        // Clear cache
        $this->cacheService->clearQuizCache($quiz->id);
        
        // Cache should be cleared
        $this->assertFalse(Cache::has('quiz:' . $quiz->id . ':'));
    }

    public function test_clear_user_stats_cache_removes_user_stats()
    {
        $user = User::factory()->create();
        
        // Populate cache
        $this->cacheService->getUserStats($user->id);
        $this->assertTrue(Cache::has('user:stats:' . $user->id));
        
        // Clear cache
        $this->cacheService->clearUserStatsCache($user->id);
        
        // Cache should be cleared
        $this->assertFalse(Cache::has('user:stats:' . $user->id));
    }

    public function test_clear_subjects_cache_removes_subjects()
    {
        // Populate cache
        $this->cacheService->getSubjects();
        $this->assertTrue(Cache::has('subjects:all'));
        
        // Clear cache
        $this->cacheService->clearSubjectsCache();
        
        // Cache should be cleared
        $this->assertFalse(Cache::has('subjects:all'));
    }

    public function test_warm_up_cache_preloads_common_data()
    {
        // Create test data
        $subject = Subject::first();
        Quiz::factory()->count(2)->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        // Warm up cache
        $this->cacheService->warmUpCache();
        
        // Check that subjects are cached
        $this->assertTrue(Cache::has('subjects:all'));
        
        // Check that grade 1 quizzes are cached for the subject
        $cacheKey = 'quiz:grade:subject:1:' . $subject->id;
        $this->assertTrue(Cache::has($cacheKey));
    }

    public function test_cache_ttl_is_respected()
    {
        $quiz = Quiz::factory()->create();
        
        // Mock cache to simulate expiration
        Cache::shouldReceive('remember')
            ->once()
            ->with(
                'quiz:' . $quiz->id . ':',
                QuizCacheService::CACHE_TTL,
                \Closure::class
            )
            ->andReturn($quiz);
        
        $result = $this->cacheService->getQuiz($quiz->id);
        $this->assertEquals($quiz->id, $result->id);
    }

    public function test_cache_handles_nonexistent_quiz()
    {
        $result = $this->cacheService->getQuiz(999);
        $this->assertNull($result);
    }

    public function test_get_quizzes_by_grade_and_subject_only_returns_published()
    {
        $subject = Subject::first();
        
        // Create published and unpublished quizzes
        $publishedQuiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => true,
        ]);
        
        $unpublishedQuiz = Quiz::factory()->create([
            'grade' => 1,
            'subject_id' => $subject->id,
            'is_published' => false,
        ]);
        
        $quizzes = $this->cacheService->getQuizzesByGradeAndSubject(1, $subject->id);
        
        $this->assertEquals(1, $quizzes->count());
        $this->assertEquals($publishedQuiz->id, $quizzes->first()->id);
    }
}