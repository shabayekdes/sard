<?php

namespace App\Http\Controllers;

use App\Models\TaskType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskTypeController extends BaseController
{
    public function index(Request $request)
    {
        $query = TaskType::withPermissionCheck()
            ->with(['creator'])
            ->where('tenant_id', createdBy());

        // Handle search - search in translatable fields
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ["%{$searchTerm}%"]);
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
            $locale = app()->getLocale();
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) asc");
        }

        $taskTypes = $query->paginate($request->per_page ?? 10);

        // Transform the data to include translated values for display and editing
        $taskTypes->getCollection()->transform(function ($taskType) {
            return [
                'id' => $taskType->id,
                'name' => $taskType->name,
                'name_translations' => $taskType->getTranslations('name'),
                'description' => $taskType->description,
                'description_translations' => $taskType->getTranslations('description'),
                'color' => $taskType->color,
                'default_duration' => $taskType->default_duration,
                'status' => $taskType->status,
                'tenant_id' => $taskType->tenant_id,
                'creator' => $taskType->creator,
                'created_at' => $taskType->created_at,
                'updated_at' => $taskType->updated_at,
            ];
        });

        return Inertia::render('tasks/task-types/index', [
            'taskTypes' => $taskTypes,
            'filters' => $request->all(['search', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'default_duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        $nameEn = $validated['name']['en'] ?? '';
        $nameAr = $validated['name']['ar'] ?? '';

        // Check if task type with same name already exists for this company
        $exists = TaskType::where('tenant_id', createdBy())
            ->where(function ($q) use ($nameEn, $nameAr) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) = ?", [$nameEn])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) = ?", [$nameAr]);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Task type with this name already exists.');
        }

        TaskType::create($validated);

        return redirect()->back()->with('success', 'Task type created successfully.');
    }

    public function update(Request $request, $taskTypeId)
    {
        $taskType = TaskType::where('id', $taskTypeId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskType) {
            return redirect()->back()->with('error', 'Task type not found.');
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'default_duration' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $nameEn = $validated['name']['en'] ?? '';
        $nameAr = $validated['name']['ar'] ?? '';

        // Check if task type with same name already exists for this company (excluding current)
        $exists = TaskType::where('tenant_id', createdBy())
            ->where('id', '!=', $taskTypeId)
            ->where(function ($q) use ($nameEn, $nameAr) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) = ?", [$nameEn])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) = ?", [$nameAr]);
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Task type with this name already exists.');
        }

        $taskType->update($validated);

        return redirect()->back()->with('success', 'Task type updated successfully.');
    }

    public function destroy($taskTypeId)
    {
        $taskType = TaskType::where('id', $taskTypeId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskType) {
            return redirect()->back()->with('error', 'Task type not found.');
        }

        try {
            $taskType->delete();
            return redirect()->back()->with('success', 'Task type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete task type.');
        }
    }

    public function toggleStatus($taskTypeId)
    {
        $taskType = TaskType::where('id', $taskTypeId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$taskType) {
            return redirect()->back()->with('error', 'Task type not found.');
        }

        try {
            $taskType->status = $taskType->status === 'active' ? 'inactive' : 'active';
            $taskType->save();

            return redirect()->back()->with('success', 'Task type status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update task type status.');
        }
    }
}