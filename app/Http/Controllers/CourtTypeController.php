<?php

namespace App\Http\Controllers;

use App\Models\CourtType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourtTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = CourtType::withPermissionCheck()
            ->with(['creator'])
            ->where(function($q) {
                $q->where('tenant_id', createdBy());
            });

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->whereJsonContains('name->en', $request->search)
                    ->orWhereJsonContains('name->ar', $request->search)
                    ->orWhereJsonContains('description->en', $request->search)
                    ->orWhereJsonContains('description->ar', $request->search);
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

        $courtTypes = $query->paginate($request->per_page ?? 10);
        
        // Transform the data to include translated values
        $courtTypes->getCollection()->transform(function ($courtType) {
            return [
                'id' => $courtType->id,
                'name' => $courtType->name, // Spatie will automatically return translated value for display
                'name_translations' => $courtType->getTranslations('name'), // Full translations for editing
                'description' => $courtType->description, // Spatie will automatically return translated value for display
                'description_translations' => $courtType->getTranslations('description'), // Full translations for editing
                'color' => $courtType->color,
                'status' => $courtType->status,
                'tenant_id' => $courtType->tenant_id,
                'created_at' => $courtType->created_at,
                'updated_at' => $courtType->updated_at,
                'creator' => $courtType->creator,
            ];
        });

        return Inertia::render('advocate/court-types/index', [
            'courtTypes' => $courtTypes,
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
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        CourtType::create($validated);

        return redirect()->back()->with('success', 'Court type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $courtType = CourtType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$courtType) {
            return redirect()->back()->with('error', 'Court type not found.');
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'status' => 'nullable|in:active,inactive',
        ]);

        $courtType->update($validated);

        return redirect()->back()->with('success', 'Court type updated successfully.');
    }

    public function destroy($id)
    {
        $courtType = CourtType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$courtType) {
            return redirect()->back()->with('error', 'Court type not found.');
        }

        $courtType->delete();

        return redirect()->back()->with('success', 'Court type deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $courtType = CourtType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$courtType) {
            return redirect()->back()->with('error', 'Court type not found.');
        }

        $courtType->status = $courtType->status === 'active' ? 'inactive' : 'active';
        $courtType->save();

        return redirect()->back()->with('success', 'Court type status updated successfully.');
    }
}