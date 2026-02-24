<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class RegulatoryBody extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'name',
        'description',
        'jurisdiction',
        'contact_email',
        'contact_phone',
        'address',
        'website',
        'status',
        'tenant_id'
    ];

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function complianceRequirements()
    {
        return $this->hasMany(ComplianceRequirement::class, 'regulatory_body_id');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->where('tenant_id', createdBy());
    // }
}