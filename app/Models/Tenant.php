<?php

namespace App\Models;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 *
 * Only attributes in getCustomColumns() are stored as table columns; the rest (name, phone, email,
 * city, company_name, business_type) are stored in the data JSON.
 */
class Tenant extends BaseTenant
{
    use HasDomains, HasScopedValidationRules;

    /**
     * Columns that exist on the tenants table. All other fillable attributes are stored in data JSON.
     *
     * @return list<string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'plan_id',
            'plan_expire_date',
            'plan_is_active',
            'requested_plan',
            'storage_limit',
            'storage_used',
            'is_trial',
            'trial_day',
            'trial_expire_date',
            'activated_at',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * plan_* and storage/trial are real columns; name, phone, email, city, company_name, business_type are in data JSON.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'city',
        'company_name',
        'business_type',
        'plan_id',
        'plan_expire_date',
        'plan_is_active',
        'requested_plan',
        'storage_limit',
        'storage_used',
        'is_trial',
        'trial_day',
        'trial_expire_date',
        'activated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'plan_expire_date' => 'date',
            'trial_expire_date' => 'date',
            'activated_at' => 'datetime',
            'plan_is_active' => 'integer',
            'requested_plan' => 'integer',
            'trial_day' => 'integer',
            'storage_limit' => 'float',
            'storage_used' => 'float',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Primary company (owner) user for this tenant.
     */
    public function companyUser(): HasOne
    {
        return $this->hasOne(User::class, 'tenant_id', 'id')
            ->where('type', 'company')
            ->orderBy('id');
    }
}
