<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Support\Facades\Config;
use Stancl\Tenancy\Events\TenancyInitialized;

/**
 * When tenancy is initialized (tenant subdomain), set app.url to the current
 * request URL so that url(), asset(), route(), Ziggy, and Inertia base_url
 * use the tenant domain (e.g. https://acme.sard.com) instead of central.
 */
class SetTenantAppUrl
{
    public function handle(TenancyInitialized $event): void
    {
        $request = request();
        if ($request) {
            Config::set('app.url', $request->getSchemeAndHttpHost());
        }
    }
}
