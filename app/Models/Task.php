<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Task extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'task_id',
        'title',
        'description',
        'priority',
        'due_date',
        'start_date',
        'estimated_duration',
        'case_id',
        'assigned_to',
        'task_type_id',
        'task_status_id',
        'notes',
        'progress',
        'tenant_id',
        'google_calendar_event_id'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'start_date' => 'datetime',
        'estimated_duration' => 'integer',
        'priority' => TaskPriority::class,
    ];

    /**
     * Boot method to auto-generate task ID
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (!$task->task_id) {
                $task->task_id = 'TSK' . str_pad(
                    (Task::max('id') ?? 0) + 1, 
                    6, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Get the case that owns the task.
     */
    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the task type that owns the task.
     */
    public function taskType()
    {
        return $this->belongsTo(TaskType::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class)->orderBy('order');
    }


    /**
     * Get the task status that owns the task.
     */
    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    /**
     * Get the user who created the task.
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest();
    }


    public function calculateProgress(): int
    {
        $checklists = $this->checklists;
        if ($checklists->isEmpty()) {
            return $this->progress;
        }

        $completed = $checklists->where('is_completed', true)->count();
        return (int) (($completed / $checklists->count()) * 100);
    }
}