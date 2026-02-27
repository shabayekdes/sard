<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = EventType::withPermissionCheck()
            ->with(['creator'])
            ->where(function($q) {
                $q->where('tenant_id', createdBy());
            });

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $eventTypes = $query->paginate($request->per_page ?? 10);

        return Inertia::render('advocate/event-types/index', [
            'eventTypes' => $eventTypes,
            'filters' => $request->all(['search', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'color' => 'required|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        EventType::create($validated);

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Event type')]));
    }

    public function update(Request $request, $id)
    {
        $eventType = EventType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$eventType) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Event type')]));
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'color' => 'required|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $eventType->update($validated);

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Event type')]));
    }

    public function destroy($id)
    {
        $eventType = EventType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$eventType) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Event type')]));
        }

        $eventType->delete();

        return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Event type')]));
    }

    public function toggleStatus($id)
    {
        $eventType = EventType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$eventType) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Event type')]));
        }

        $eventType->status = $eventType->status === 'active' ? 'inactive' : 'active';
        $eventType->save();

        return redirect()->back()->with('success', __(':model status updated successfully', ['model' => __('Event type')]));
    }
}