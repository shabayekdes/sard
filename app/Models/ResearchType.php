<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class ResearchType extends BaseModel
{
    use HasFactory, HasTranslations, AutoApplyPermissionCheck;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'created_by'
    ];

    /**
     * Get the user who created the research type.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the research projects assigned to this type.
     */
    public function researchProjects()
    {
        return $this->hasMany(ResearchProject::class);
    }
}