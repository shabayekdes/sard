<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\CaseActivityLogger;

class TaskCaseActivityObserver
{
    private const WATCH_FIELDS = [
        'title',
        'description',
        'due_date',
        'start_date',
        'assigned_to',
        'task_type_id',
        'task_status_id',
        'priority',
        'notes',
        'progress',
    ];

    public function created(Task $task): void
    {
        if (! $task->case_id) {
            return;
        }
        CaseActivityLogger::taskCreated($task);
    }

    public function updated(Task $task): void
    {
        if (! $task->case_id) {
            return;
        }

        if ($task->wasChanged('task_status_id')) {
            $oldId = $task->getOriginal('task_status_id');
            $newId = $task->task_status_id;
            if (CaseActivityLogger::taskStatusIsCompleted((int) $newId)
                && ! CaseActivityLogger::taskStatusIsCompleted((int) $oldId)) {
                CaseActivityLogger::taskCompleted($task);

                return;
            }
        }

        foreach (self::WATCH_FIELDS as $field) {
            if ($task->wasChanged($field)) {
                CaseActivityLogger::taskUpdated($task);

                return;
            }
        }
    }

    public function deleting(Task $task): void
    {
        if (! $task->case_id) {
            return;
        }
        CaseActivityLogger::taskDeleted($task);
    }
}
