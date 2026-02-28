<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLandingPageEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If accessing home and landing page is disabled, redirect to login on same domain (avoid 127.0.0.1 when behind proxy)
        if (!\App\Facades\Settings::boolean('LANDING_PAGE_ENABLED') && $request->route()->getName() === 'home') {
            $baseUrl = $request->getSchemeAndHttpHost();
            return redirect()->away($baseUrl . '/login');
        }

        return $next($request);
    }
}