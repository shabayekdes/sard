<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $emailVerificationEnabled = Settings::boolean('ENABLE_EMAIL_VERIFICATION');
        
        if ($emailVerificationEnabled &&
            $request->user() &&
            $request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail()) {
            // Redirect to verification notice on current host (tenant or central) to avoid sending user to SaaS domain
            return redirect()->away($request->getSchemeAndHttpHost() . '/verify-email');
        }

        return $next($request);
    }
}