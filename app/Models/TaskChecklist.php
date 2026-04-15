<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChecklist extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'is_completed',
        'order',
        'assigned_to',
        'due_date',
        'created_by'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'order' => 'integer',
        'due_date' => 'date'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function toggle(): void
    {
        $this->update(['is_completed' => !$this->is_completed]);

        // Update parent task progress
        $this->task->update([
            'progress' => $this->task->calculateProgress()
        ]);
    }

    public function canBeUpdatedBy(User $user): bool
    {
        if ($this->created_by !== null) {
            return (int) $this->created_by === (int) $user->id;
        }

        // Legacy rows with no creator: allow the task assignee to manage checklist items
        $task = $this->task;
        if ($task && $task->assigned_to !== null) {
            return (int) $task->assigned_to === (int) $user->id;
        }

        return false;
    }

    public function canBeDeletedBy(User $user): bool
    {
        if ($this->created_by !== null) {
            return (int) $this->created_by === (int) $user->id;
        }

        $task = $this->task;
        if ($task && $task->assigned_to !== null) {
            return (int) $task->assigned_to === (int) $user->id;
        }

        return false;
    }
}
