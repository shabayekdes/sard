<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasScopedValidationRules;

/**
 * Tenant = company (User with type=company).
 * id matches the company User id for single-database tenancy.
 *
 * Supports combined domain/subdomain identification: add domains via $tenant->createDomain('subdomain')
 * (no dots = subdomain on central domain) or $tenant->createDomain('custom.domain.com').
 *
 * For tenant-scoped validation use: tenant()->unique('table', 'column') or tenant()->exists('table')
 * (from HasScopedValidationRules), or Rule::unique('table','col')->where('tenant_id', tenant('id'))
 */
class Tenant extends BaseTenant
{
    use HasDomains, HasScopedValidationRules;
}
