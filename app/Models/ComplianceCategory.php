<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ComplianceCategory extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

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

    public function complianceRequirements()
    {
        return $this->hasMany(ComplianceRequirement::class, 'category_id');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->where('created_by', createdBy());
    // }
}