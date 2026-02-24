<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class AuditType extends BaseModel
{
    use BelongsToTenant, AutoApplyPermissionCheck;
    
    protected $fillable = [
        'name',
        'description',
        'color',
        'status',
        'tenant_id'
    ];

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function complianceAudits()
    {
        return $this->hasMany(ComplianceAudit::class);
    }
}