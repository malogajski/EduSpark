<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\LocaleMiddleware::class,
        ]);
        
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'quiz.access' => \App\Http\Middleware\HandleQuizAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            // Handle 403 errors with custom message
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => $e->getMessage()], 403);
                }
                
                session()->flash('error', $e->getMessage());
                return redirect()->route('home');
            }
            
            // Handle 404 errors
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Page not found'], 404);
                }
                
                session()->flash('error', 'The page you are looking for could not be found.');
                return redirect()->route('home');
            }
            
            // Handle database connection errors
            if ($e instanceof \Illuminate\Database\QueryException) {
                \Log::error('Database error: ' . $e->getMessage());
                
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Database connection error'], 500);
                }
                
                session()->flash('error', 'A database error occurred. Please try again later.');
                return redirect()->route('home');
            }
            
            return null; // Let Laravel handle other exceptions normally
        });
    })->create();
