<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskStatusController extends BaseController
{
    public function index(Request $request)
    {
        $query = TaskStatus::withPermissionCheck()
            ->with(['creator'])
            ->where('tenant_id', createdBy());

        // Handle search - search in translatable name
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        // Handle status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $sortField = $request->sort_field;
            $sortDir = $request->sort_direction ?? 'asc';
            if ($sortField === 'name') {
                $locale = app()->getLocale();
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) {$sortDir}");
            } else {
                $query->orderBy($sortField, $sortDir);
            }
        } else {
            $query->latest('id');
        }

        $taskStatuses = $query->paginate($request->per_page ?? 10);

        $taskStatuses->getCollection()->transform(function ($taskStatus) {
            return [
                'id' => $taskStatus->id,
                'name' => $taskStatus->name,
                'name_translations' => $taskStatus->getTranslations('name'),
                'color' => $taskStatus->color,
                'is_completed' => $taskStatus->is_completed,
                'status' => $taskStatus->status,
                'tenant_id' => $taskStatus->tenant_id,
                'creator' => $taskStatus->creator,
                'created_at' => $taskStatus->created_at,
                'updated_at' => $taskStatus->updated_at,
            ];
        });

        return Inertia::render('tasks/task-statuses/index', [
            'taskStatuses' => $taskStatuses,
            'filters' => $request->all(['search', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_completed' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['is_completed'] = $validated['is_completed'] ?? false;

        $nameEn = $validated['name']['en'] ?? '';
        $nameAr = $validated['name']['ar'] ?? '';

        // Check if task status with same name already exists for this company
        $exists = TaskStatus::where('tenant_id', createdBy())
            ->where(function ($q) use ($nameEn, $nameAr) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) = ?", [$nameEn])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) = ?", [$nameAr]);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', __(':model with this name already exists.', ['model' => __('Task Status')]));
        }

        TaskStatus::create($validated);

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Task Status')]));
    }

    public function update(Request $request, $taskStatusId)
    {
        $taskStatus = TaskStatus::where('id', $taskStatusId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskStatus) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Task Status')]));
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_completed' => 'required|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $nameEn = $validated['name']['en'] ?? '';
        $nameAr = $validated['name']['ar'] ?? '';

        // Check if task status with same name already exists for this company (excluding current)
        $exists = TaskStatus::where('tenant_id', createdBy())
            ->where('id', '!=', $taskStatusId)
            ->where(function ($q) use ($nameEn, $nameAr) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) = ?", [$nameEn])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) = ?", [$nameAr]);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', __(':model with this name already exists.', ['model' => __('Task Status')]));
        }

        $taskStatus->update($validated);

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Task Status')]));
    }

    public function destroy($taskStatusId)
    {
        $taskStatus = TaskStatus::where('id', $taskStatusId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskStatus) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Task Status')]));
        }

        try {
            $taskStatus->delete();
            return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Task Status')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to delete :model', ['model' => __('Task Status')]));
        }
    }

    public function toggleStatus($taskStatusId)
    {
        $taskStatus = TaskStatus::where('id', $taskStatusId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskStatus) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Task Status')]));
        }

        try {
            $taskStatus->status = $taskStatus->status === 'active' ? 'inactive' : 'active';
            $taskStatus->save();

            return redirect()->back()->with('success', __(':model status updated successfully', ['model' => __('Task Status')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update :model status', ['model' => __('Task Status')]));
        }
    }
}