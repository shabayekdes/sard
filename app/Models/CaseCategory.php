<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseCategory extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'color',
        'status',
        'tenant_id'
    ];

    public function parent()
    {
        return $this->belongsTo(CaseCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CaseCategory::class, 'parent_id');
    }

    public function cases()
    {
        return $this->hasMany(CaseModel::class, 'case_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

