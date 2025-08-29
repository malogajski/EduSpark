<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Attempt;
use Symfony\Component\HttpFoundation\Response;

class HandleQuizAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get quiz from route parameter (could be model or ID)
        $quiz = $request->route('quiz');

        if (!$quiz) {
            return $next($request);
        }

        try {
            // Handle both Quiz model and ID
            if ($quiz instanceof Quiz) {
                $quizModel = $quiz;
            } else {
                $quizModel = Quiz::findOrFail($quiz);
            }

            // Check if quiz is published
            if (!$quiz->is_published) {
                abort(404, 'Quiz not found or not published.');
            }

            // For quiz play route, check if there's an active attempt
            if ($request->route()->getName() === 'quiz.play') {
                $attempt = Attempt::where('quiz_id', $quiz->id)
                    ->where(function($query) {
                        if (auth()->check()) {
                            $query->where('user_id', auth()->id());
                        } else {
                            $query->whereNotNull('guest_name');
                        }
                    })
                    ->whereNull('finished_at')
                    ->latest()
                    ->first();

                if (!$attempt) {
                    session()->flash('error', 'No active quiz attempt found. Please start the quiz again.');
                    return redirect()->route('quiz.start');
                }
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Quiz not found.');
        } catch (\Exception $e) {
            abort(500, 'An error occurred while accessing the quiz.');
        }

        return $next($request);
    }
}
