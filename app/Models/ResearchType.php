<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ResearchType extends BaseModel
{
    use BelongsToTenant, HasFactory, HasTranslations, AutoApplyPermissionCheck;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'tenant_id'
    ];

    /**
     * Get the user who created the research type.
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    /**
     * Get the research projects assigned to this type.
     */
    public function researchProjects()
    {
        return $this->hasMany(ResearchProject::class);
    }
}