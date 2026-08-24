<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check if locale is present in the URL segments (e.g., /es/events)
        $locale = $request->segment(1);

        $availableLocales = ['en', 'es', 'ht'];

        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            
            // Set default locale for URL generation (named routes will auto-include it)
            URL::defaults(['locale' => $locale]);
        } else {
            // 2. Fallback to Session or Default
            $locale = Session::get('locale', config('app.locale', 'en'));
            App::setLocale($locale);
        }

        return $next($request);
    }
}
