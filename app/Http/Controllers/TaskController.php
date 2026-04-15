<?php

namespace App\Http\Controllers;

use App\Events\NewTaskCreated;
use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\CaseModel;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TaskController extends BaseController
{
    public function index(Request $request)
    {
        $query = Task::withPermissionCheck()
            ->with(['taskType', 'taskStatus', 'assignedUser', 'case', 'creator']);

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('task_id', 'like', '%' . $request->search . '%');
            });
        }

        // Handle task type filter
        if ($request->has('task_type_id') && !empty($request->task_type_id) && $request->task_type_id !== 'all') {
            $query->where('task_type_id', $request->task_type_id);
        }

        // Handle task status filter (custom TaskStatus records)
        if ($request->has('task_status_id') && !empty($request->task_status_id) && $request->task_status_id !== 'all') {
            $query->where('task_status_id', $request->task_status_id);
        }

        // Handle priority filter
        if ($request->has('priority') && !empty($request->priority) && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Handle assigned user filter
        if ($request->has('assigned_to') && !empty($request->assigned_to) && $request->assigned_to !== 'all') {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Handle sorting
        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('due_date', 'asc');
        }

        $tasks = $query->paginate($request->per_page ?? 10);

        // Get filter options
        $taskTypes = TaskType::where('tenant_id', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $users = User::where('tenant_id', createdBy())
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            })
            ->orWhere('id', createdBy())
            ->get(['id', 'name']);

        $cases = CaseModel::where('tenant_id', createdBy())
            ->get(['id', 'case_id', 'title']);

        $taskStatuses = TaskStatus::where('tenant_id', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        return Inertia::render('tasks/index', [
            'tasks' => $tasks,
            'taskTypes' => $taskTypes,
            'projects' => [],
            'users' => $users,
            'cases' => $cases,
            'taskStatuses' => $taskStatuses,
            'filters' => $request->all(['search', 'task_type_id', 'priority', 'assigned_to', 'task_status_id', 'view', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'due_date' => 'nullable|date',
            'estimated_duration' => 'nullable|integer|min:1',
            'case_id' => 'nullable|exists:cases,id',
            'assigned_to' => 'nullable|exists:users,id',
            'task_type_id' => 'nullable|exists:task_types,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'notes' => 'nullable|string',
        ]);

        $validated['tenant_id'] = createdBy();

        // Validate that related records belong to the current user's company
        if (!empty($validated['case_id'])) {
            $case = CaseModel::where('id', $validated['case_id'])
                ->where('tenant_id', createdBy())
                ->first();
            if (!$case) {
                return redirect()->back()->with('error', __('Invalid case selected.'));
            }
        }

        if (!empty($validated['assigned_to'])) {
            $user = User::where('id', $validated['assigned_to'])
                ->where(function ($q) {
                    $q->where('tenant_id', createdBy())
                        ->orWhere('id', createdBy());
                })
                ->first();
            if (!$user) {
                return redirect()->back()->with('error', __('Invalid user selected.'));
            }
        }

        if (!empty($validated['task_type_id'])) {
            $taskType = TaskType::where('id', $validated['task_type_id'])
                ->where('tenant_id', createdBy())
                ->first();
            if (!$taskType) {
                return redirect()->back()->with('error', __('Invalid task type selected.'));
            }
        }

        if (!empty($validated['task_status_id'])) {
            $taskStatus = TaskStatus::where('id', $validated['task_status_id'])
                ->where('tenant_id', createdBy())
                ->first();
            if (!$taskStatus) {
                return redirect()->back()->with('error', __('Invalid task status selected.'));
            }
        }

        $task = Task::create($validated);

        // Handle Google Calendar sync
        // if ($task && $request->sync_with_google_calendar) {
        //     $calendarService = new GoogleCalendarService();
        //     $eventId = $calendarService->createEvent($task, createdBy(), 'task');
        //     if ($eventId) {
        //         $task->update(['google_calendar_event_id' => $eventId]);
        //     }
        // }

        // Trigger notifications
        event(new \App\Events\NewTaskCreated($task, $request->all()));

        // Check for errors and combine them
        $emailError = session()->pull('email_error');
        $slackError = session()->pull('slack_error');

        $errors = [];
        if ($emailError) {
            $errors[] = __('Email send failed: ') . $emailError;
        }
        if ($slackError) {
            $errors[] = __('SMS send failed: ') . $slackError;
        }

        if (!empty($errors)) {
            $message = __('Task created successfully, but ') . implode(', ', $errors);
            return redirect()->back()->with('warning', $message);
        }

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Task')]));
    }

    public function update(Request $request, $taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('tenant_id', createdBy())
            ->first();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'due_date' => 'nullable|date',
            'estimated_duration' => 'nullable|integer|min:1',
            'case_id' => 'nullable|exists:cases,id',
            'assigned_to' => 'nullable|exists:users,id',
            'task_type_id' => 'nullable|exists:task_types,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'notes' => 'nullable|string',
        ]);

        // Validate that related records belong to the current user's company
        if (!empty($validated['case_id'])) {
            $case = CaseModel::where('id', $validated['case_id'])
                ->where('tenant_id', createdBy())
                ->first();
            if (!$case) {
                return redirect()->back()->with('error', __('Invalid case selected.'));
            }
        }

        if (!empty($validated['assigned_to'])) {
            $user = User::where('id', $validated['assigned_to'])
                ->where(function ($q) {
                    $q->where('tenant_id', createdBy())
                        ->orWhere('id', createdBy());
                })
                ->first();
            if (!$user) {
                return redirect()->back()->with('error', __('Invalid user selected.'));
            }
        }

        if (!empty($validated['task_type_id'])) {
            $taskType = TaskType::where('id', $validated['task_type_id'])
                ->where('tenant_id', createdBy())
                ->first();
            if (!$taskType) {
                return redirect()->back()->with('error', __('Invalid task type selected.'));
            }
        }

        if (!empty($validated['task_status_id'])) {
            $taskStatus = TaskStatus::where('id', $validated['task_status_id'])
                ->where('tenant_id', createdBy())
                ->first();
            if (!$taskStatus) {
                return redirect()->back()->with('error', __('Invalid task status selected.'));
            }
        }

        $task->update($validated);

        // Handle Google Calendar sync
        // if ($request->sync_with_google_calendar && !$task->google_calendar_event_id) {
        //     $calendarService = new GoogleCalendarService();
        //     $eventId = $calendarService->createEvent($task, createdBy(), 'task');
        //     if ($eventId) {
        //         $task->update(['google_calendar_event_id' => $eventId]);
        //     }
        // } elseif ($request->sync_with_google_calendar && $task->google_calendar_event_id) {
        //     $calendarService = new GoogleCalendarService();
        //     $calendarService->updateEvent($task->google_calendar_event_id, $task, createdBy(), 'task');
        // } elseif (!$request->sync_with_google_calendar && $task->google_calendar_event_id) {
        //     $calendarService = new GoogleCalendarService();
        //     $calendarService->deleteEvent($task->google_calendar_event_id, createdBy());
        //     $task->update(['google_calendar_event_id' => null]);
        // }

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Task')]));
    }


    public function show(Task $task)
    {
        // $this->authorizePermission('task_view');

        $task->load([
            'case',
            'taskStatus',
            'assignedUser',
            'creator',
            // 'milestone',
            'comments.user',
            'checklists.assignedTo',
            'checklists.creator',
            // 'attachments.mediaItem'
        ]);

        // Ensure MediaItem appended attributes are loaded
        // $task->attachments->load('mediaItem');
        // $task->attachments->each(function ($attachment) {
        //     if ($attachment->mediaItem) {
        //         // Force load the media to ensure appended attributes work
        //         $attachment->mediaItem->getFirstMedia('images');
        //     }
        // });

        $currentUser = auth()->user();

        // Add permission flags to comments
        $task->comments?->each(function ($comment) use ($currentUser) {
            $comment->can_update = $comment->canBeUpdatedBy($currentUser);
            $comment->can_delete = $comment->canBeDeletedBy($currentUser);
        });

        // Add permission flags to checklists
        $task->checklists?->each(function ($checklist) use ($currentUser) {
            $checklist->can_update = true;//$checklist->canBeUpdatedBy($currentUser);
            $checklist->can_delete = true;//$checklist->canBeDeletedBy($currentUser);
        });

        $allMembers = User::all();

        // Get project members only (no clients)
        // $projectMembers = $task->project->members->filter(function ($member) {
        //     return $member->user && $member->user->type !== 'client';
        // })->pluck('user');

        $taskStatuses = TaskStatus::where('tenant_id', $task->tenant_id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        return response()->json([
            'task' => $task,
            'members' => [], //$projectMembers->isNotEmpty() ? $projectMembers : $allMembers,
            'taskStatuses' => $taskStatuses,
            'milestones' => [],//$milestones,
            'permissions' => [
                'update' => true,
                'delete' => true,
                'duplicate' => true,
                'change_status' => true,
                'assign_users' => true,
                'add_comments' => true,
                'add_attachments' => true,
                'manage_checklists' => true,
            ]
        ]);
    }

    public function destroy($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('tenant_id', createdBy())
            ->first();

        try {
            // Delete Google Calendar event if exists
            if ($task->google_calendar_event_id) {
                $calendarService = new GoogleCalendarService();
                $calendarService->deleteEvent($task->google_calendar_event_id, createdBy());
            }
            
            $task->delete();
            return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Task')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to delete :model', ['model' => __('Task')]));
        }
    }

    public function getCaseUsers($caseId)
    {
        $case = CaseModel::where('id', $caseId)
            ->where('tenant_id', createdBy())
            ->with('teamMembers.user')
            ->first();

        if (!$case) {
            return response()->json(['users' => []]);
        }

        $users = $case->teamMembers->map(function ($teamMember) {
            return [
                'value' => $teamMember->user->id,
                'label' => $teamMember->user->name
            ];
        });

        return response()->json($users);
    }


    public function duplicate(Task $task)
    {
        $this->authorizePermission('task_duplicate');

        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if (!$workspace || $task->project->workspace_id != $workspace->id) {
            abort(403, 'Task not found in current workspace.');
        }
        $newTask = $task->replicate();
        $newTask->title = $task->title . ' (Copy)';
        $newTask->start_date = null;
        $newTask->end_date = null;
        $newTask->progress = 0;
        $newTask->created_by = auth()->id();
        $newTask->save();

        // Copy checklists
        foreach ($task->checklists as $checklist) {
            $newChecklist = $checklist->replicate();
            $newChecklist->task_id = $newTask->id;
            $newChecklist->is_completed = false;
            $newChecklist->created_by = auth()->id();
            $newChecklist->save();
        }

        return back()->with('success', __('Task duplicated successfully!'));
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        if ((string) $task->tenant_id !== (string) createdBy()) {
            abort(403);
        }

        $validated = $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        $taskStatus = TaskStatus::where('id', $validated['task_status_id'])
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskStatus) {
            return redirect()->back()->with('error', __('Invalid selection. Please try again.'));
        }

        $task->update(['task_status_id' => $taskStatus->id]);

        return redirect()->back()->with('success', __(':model status updated successfully', ['model' => __('Task')]));
    }

    /**
     * Get tasks for calendar view (including Google Calendar tasks)
     */
    public function getCalendarTasks(Request $request)
    {
        $calendarView = $request->get('calendar_view', 'local'); // 'local' or 'google'

        $tasks = Task::withPermissionCheck()
            ->with(['case', 'taskStatus', 'assignedUser'])
            ->when($calendarView === 'google', function ($query) {
                $query->whereNotNull('google_calendar_event_id');
            })
            ->get();

        return response()->json([
            'tasks' => $tasks,
            'calendar_view' => $calendarView
        ]);
    }
}
