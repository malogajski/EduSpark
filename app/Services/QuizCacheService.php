<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Attempt;
use Illuminate\Support\Facades\Cache;

class QuizCacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const QUIZ_KEY = 'quiz:';
    const SUBJECTS_KEY = 'subjects:all';
    const QUIZ_BY_GRADE_SUBJECT_KEY = 'quiz:grade:subject:';
    const USER_STATS_KEY = 'user:stats:';

    public function getQuiz($quizId, $withRelations = [])
    {
        $key = self::QUIZ_KEY . $quizId . ':' . implode(',', $withRelations);
        
        return Cache::remember($key, self::CACHE_TTL, function () use ($quizId, $withRelations) {
            $query = Quiz::query();
            
            if (!empty($withRelations)) {
                $query->with($withRelations);
            }
            
            return $query->find($quizId);
        });
    }

    public function getSubjects()
    {
        return Cache::remember(self::SUBJECTS_KEY, self::CACHE_TTL, function () {
            return Subject::orderBy('name->sr')->get();
        });
    }

    public function getQuizzesByGradeAndSubject($grade, $subjectId)
    {
        $key = self::QUIZ_BY_GRADE_SUBJECT_KEY . $grade . ':' . $subjectId;
        
        return Cache::remember($key, self::CACHE_TTL, function () use ($grade, $subjectId) {
            return Quiz::where('grade', $grade)
                ->where('subject_id', $subjectId)
                ->where('is_published', true)
                ->with('subject')
                ->get();
        });
    }

    public function getUserStats($userId)
    {
        $key = self::USER_STATS_KEY . $userId;
        
        return Cache::remember($key, 300, function () use ($userId) { // 5 minutes cache for stats
            $attempts = Attempt::where('user_id', $userId)
                ->whereNotNull('finished_at')
                ->with('quiz.questions')
                ->get();
                
            $totalAttempts = $attempts->count();
            
            if ($totalAttempts === 0) {
                return [
                    'total_attempts' => 0,
                    'average_score' => 0,
                    'best_score' => 0,
                ];
            }
            
            $totalPercentage = $attempts->sum(function ($attempt) {
                $totalQuestions = $attempt->quiz->questions->count();
                return $totalQuestions > 0 ? ($attempt->score / $totalQuestions) * 100 : 0;
            });
            
            $averageScore = round($totalPercentage / $totalAttempts, 1);
            
            $bestScore = $attempts->max(function ($attempt) {
                $totalQuestions = $attempt->quiz->questions->count();
                return $totalQuestions > 0 ? ($attempt->score / $totalQuestions) * 100 : 0;
            });
            
            return [
                'total_attempts' => $totalAttempts,
                'average_score' => $averageScore,
                'best_score' => round($bestScore, 1),
            ];
        });
    }

    public function clearQuizCache($quizId)
    {
        $patterns = [
            self::QUIZ_KEY . $quizId . '*',
            self::QUIZ_BY_GRADE_SUBJECT_KEY . '*',
        ];
        
        foreach ($patterns as $pattern) {
            $keys = Cache::store()->keys($pattern);
            if (!empty($keys)) {
                Cache::deleteMultiple($keys);
            }
        }
    }

    public function clearUserStatsCache($userId)
    {
        Cache::forget(self::USER_STATS_KEY . $userId);
    }

    public function clearSubjectsCache()
    {
        Cache::forget(self::SUBJECTS_KEY);
    }

    public function warmUpCache()
    {
        // Warm up subjects cache
        $this->getSubjects();
        
        // Warm up popular quizzes (grade 1-3)
        $subjects = Subject::all();
        foreach ($subjects as $subject) {
            for ($grade = 1; $grade <= 3; $grade++) {
                $this->getQuizzesByGradeAndSubject($grade, $subject->id);
            }
        }
    }
}