<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const ALLOWED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->validLocale(auth()->check() ? auth()->user()->lang : null)
            ?? $this->validLocale($request->cookie('app_language'))
            ?? 'en';

        app()->setLocale($locale);

        $response = $next($request);

        return $response->cookie('app_language', $locale, 60 * 24 * 30, '/');
    }

    /** Return normalized locale if allowed, null otherwise. */
    private function validLocale(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $base = strtolower(trim(explode('-', $value)[0] ?? $value));
        return in_array($base, self::ALLOWED, true) ? $base : null;
    }
}
