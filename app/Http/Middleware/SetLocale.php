<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Facades\Settings;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const ALLOWED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = $this->validLocale(config('app.locale')) ?? 'en';

        $locale = $this->validLocale(auth()->check() ? auth()->user()->lang : null)
            ?? $this->validLocale(Settings::string('DEFAULT_LANGUAGE'))
            ?? $defaultLocale;

        app()->setLocale($locale);

        $response = $next($request);

        // StreamedResponse (e.g. file download) has no cookie() method
        if (method_exists($response, 'cookie')) {
            $response->cookie('app_language', $locale, 60 * 24 * 30, '/');
        }
        return $response;
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
