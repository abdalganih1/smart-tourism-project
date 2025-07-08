<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Import App Facade
use Illuminate\Support\Facades\Session; // Import Session Facade

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if a locale is stored in the session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Optional: Set a default locale if none is in session (e.g., from browser header)
            // App::setLocale($request->getPreferredLanguage(['en', 'ar'])); // Example
            // Or simply use the locale defined in config/app.php
            // App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}