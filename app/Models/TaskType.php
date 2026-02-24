<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TaskType extends BaseModel
{
    use BelongsToTenant, HasFactory, HasTranslations, AutoApplyPermissionCheck;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'color',
        'default_duration',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'default_duration' => 'integer',
    ];

    /**
     * Get the user who created the task type.
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    /**
     * Get the tasks for this task type.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}