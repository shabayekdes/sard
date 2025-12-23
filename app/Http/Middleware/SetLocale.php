<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from cookie, user preference, or default to 'en'
        $locale = Cookie::get('app_language') ?? (auth()->check() ? auth()->user()->lang : null) ?? 'en';

        // Set the application locale
        app()->setLocale($locale);

        return $next($request);
    }
}
