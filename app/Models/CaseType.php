<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseType extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'case_category_id',
        'color',
        'status',
        'tenant_id'
    ];

    public function cases()
    {
        return $this->hasMany(CaseModel::class);
    }

    public function caseCategory()
    {
        return $this->belongsTo(CaseCategory::class, 'case_category_id');
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->where('created_by', createdBy());
    // }
}