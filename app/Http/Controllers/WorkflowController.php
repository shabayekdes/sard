<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkflowController extends BaseController
{
    public function index(Request $request)
    {
        $query = Workflow::withPermissionCheck()
            ->with(['creator'])
            ->where('tenant_id', createdBy());

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('name', 'asc');
        }

        $workflows = $query->paginate($request->per_page ?? 10);

        return Inertia::render('tasks/workflows/index', [
            'workflows' => $workflows,
            'filters' => $request->all(['search', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_event' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        Workflow::create($validated);

        return redirect()->back()->with('success', 'Workflow created successfully.');
    }

    public function update(Request $request, $workflowId)
    {
        $workflow = Workflow::where('id', $workflowId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$workflow) {
            return redirect()->back()->with('error', 'Workflow not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_event' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $workflow->update($validated);

        return redirect()->back()->with('success', 'Workflow updated successfully.');
    }

    public function destroy($workflowId)
    {
        $workflow = Workflow::where('id', $workflowId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$workflow) {
            return redirect()->back()->with('error', 'Workflow not found.');
        }

        try {
            $workflow->delete();
            return redirect()->back()->with('success', 'Workflow deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete workflow.');
        }
    }

    public function toggleStatus($workflowId)
    {
        $workflow = Workflow::where('id', $workflowId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$workflow) {
            return redirect()->back()->with('error', 'Workflow not found.');
        }

        try {
            $workflow->status = $workflow->status === 'active' ? 'inactive' : 'active';
            $workflow->save();

            return redirect()->back()->with('success', 'Workflow status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update workflow status.');
        }
    }
}