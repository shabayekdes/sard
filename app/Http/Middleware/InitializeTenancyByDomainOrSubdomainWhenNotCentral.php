<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Symfony\Component\HttpFoundation\Response;

/**
 * Combined domain/subdomain identification: run only when the request host
 * is not a central domain. On central domain we skip so auth-based tenancy
 * (InitializeTenancyByAuth) can run later.
 */
class InitializeTenancyByDomainOrSubdomainWhenNotCentral
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);

        if (in_array($host, $centralDomains, true)) {
            return $next($request);
        }

        if ($this->isSubdomain($host, $centralDomains)) {
            return app(InitializeTenancyBySubdomain::class)->handle($request, $next);
        }

        return app(InitializeTenancyByDomain::class)->handle($request, $next);
    }

    protected function isSubdomain(string $hostname, array $centralDomains): bool
    {
        foreach ($centralDomains as $central) {
            if (Str::endsWith($hostname, $central)) {
                return true;
            }
        }

        return false;
    }
}
