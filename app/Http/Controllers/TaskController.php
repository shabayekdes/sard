<?php

namespace App\Http\Controllers;

use App\Events\NewTaskCreated;
use App\Facades\Settings;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\CaseModel;
use App\Models\Setting;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Handle status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
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

        $googleCalendarEnabled = Settings::boolean('GOOGLE_CALENDAR_ENABLED');

        return Inertia::render('tasks/index', [
            'tasks' => $tasks,
            'taskTypes' => $taskTypes,
            'users' => $users,
            'cases' => $cases,
            'taskStatuses' => $taskStatuses,
            'googleCalendarEnabled' => $googleCalendarEnabled,
            'filters' => $request->all(['search', 'task_type_id', 'status', 'priority', 'assigned_to', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:critical,high,medium,low',
            'status' => 'nullable|in:not_started,in_progress,completed,on_hold',
            'due_date' => 'nullable|date',
            'estimated_duration' => 'nullable|integer|min:1',
            'case_id' => 'nullable|exists:cases,id',
            'assigned_to' => 'nullable|exists:users,id',
            'task_type_id' => 'nullable|exists:task_types,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'notes' => 'nullable|string',
            'sync_with_google_calendar' => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'not_started';

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
        if ($task && $request->sync_with_google_calendar) {
            $calendarService = new GoogleCalendarService();
            $eventId = $calendarService->createEvent($task, createdBy(), 'task');
            if ($eventId) {
                $task->update(['google_calendar_event_id' => $eventId]);
            }
        }

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
            'priority' => 'required|in:critical,high,medium,low',
            'status' => 'required|in:not_started,in_progress,completed,on_hold',
            'due_date' => 'nullable|date',
            'estimated_duration' => 'nullable|integer|min:1',
            'case_id' => 'nullable|exists:cases,id',
            'assigned_to' => 'nullable|exists:users,id',
            'task_type_id' => 'nullable|exists:task_types,id',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'notes' => 'nullable|string',
            'sync_with_google_calendar' => 'nullable|boolean',
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
        if ($request->sync_with_google_calendar && !$task->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $eventId = $calendarService->createEvent($task, createdBy(), 'task');
            if ($eventId) {
                $task->update(['google_calendar_event_id' => $eventId]);
            }
        } elseif ($request->sync_with_google_calendar && $task->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $calendarService->updateEvent($task->google_calendar_event_id, $task, createdBy(), 'task');
        } elseif (!$request->sync_with_google_calendar && $task->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $calendarService->deleteEvent($task->google_calendar_event_id, createdBy());
            $task->update(['google_calendar_event_id' => null]);
        }

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Task')]));
    }

    public function show($taskId)
    {
        $task = Task::withPermissionCheck()->with(['taskType', 'taskStatus', 'assignedUser', 'case', 'creator'])
            ->where('id', $taskId)
            ->first();

        return Inertia::render('tasks/show', [
            'task' => $task,
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

    public function toggleStatus($taskId)
    {
        $task = Task::where('id', $taskId)
            ->where('tenant_id', createdBy())
            ->first();

        try {
            $newStatus = $task->status === 'completed' ? 'not_started' : 'completed';
            $task->status = $newStatus;
            $task->save();

            return redirect()->back()->with('success', __(':model status updated successfully', ['model' => __('Task')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update :model status', ['model' => __('Task')]));
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
}
