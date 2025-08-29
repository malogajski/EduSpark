<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.available_locales', ['sr', 'en', 'hu']);
        $locale = $this->determineLocale($request, $availableLocales);
        
        App::setLocale($locale);
        session()->put('locale', $locale);

        return $next($request);
    }

    private function determineLocale(Request $request, array $availableLocales): string
    {
        // Priority: query parameter > session > user preference > default
        if ($request->has('lang') && in_array($request->get('lang'), $availableLocales)) {
            return $request->get('lang');
        }

        if (session()->has('locale') && in_array(session('locale'), $availableLocales)) {
            return session('locale');
        }

        if (auth()->check() && auth()->user()->locale && in_array(auth()->user()->locale, $availableLocales)) {
            return auth()->user()->locale;
        }

        return config('app.locale', 'sr');
    }
}