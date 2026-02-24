<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Initialize tenancy from the authenticated user.
 * Tenant = company (createdBy() for non-superadmin users).
 * Superadmin: do not set tenant so BelongsToTenant does not restrict.
 */
class InitializeTenancyByAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (tenancy()->initialized) {
            return $next($request);
        }

        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Superadmin: do not initialize tenant so they see all data
        if ($user->hasRole(['superadmin'])) {
            return $next($request);
        }

        $tenantId = createdBy();
        if (! $tenantId) {
            return $next($request);
        }

        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            return $next($request);
        }

        tenancy()->initialize($tenant);

        return $next($request);
    }
}
