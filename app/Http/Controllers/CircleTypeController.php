<?php

namespace App\Http\Controllers;

use App\Models\CircleType;
use App\Models\Court;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CircleTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = CircleType::withPermissionCheck()
            ->with(['creator'])
            ->where(function($q) {
                $q->where('created_by', createdBy());
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

        $circleTypes = $query->paginate($request->per_page ?? 10);
        
        // Transform the data to include translated values
        $circleTypes->getCollection()->transform(function ($circleType) {
            return [
                'id' => $circleType->id,
                'name' => $circleType->name, // Spatie will automatically return translated value for display
                'name_translations' => $circleType->getTranslations('name'), // Full translations for editing
                'description' => $circleType->description, // Spatie will automatically return translated value for display
                'description_translations' => $circleType->getTranslations('description'), // Full translations for editing
                'color' => $circleType->color,
                'status' => $circleType->status,
                'created_by' => $circleType->created_by,
                'created_at' => $circleType->created_at,
                'updated_at' => $circleType->updated_at,
                'creator' => $circleType->creator,
            ];
        });

        return Inertia::render('advocate/circle-types/index', [
            'circleTypes' => $circleTypes,
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

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        CircleType::create($validated);

        return redirect()->back()->with('success', 'Circle type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $circleType = CircleType::where('id', $id)
            ->where('created_by', createdBy())
            ->first();

        if (!$circleType) {
            return redirect()->back()->with('error', 'Circle type not found.');
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

        $circleType->update($validated);

        return redirect()->back()->with('success', 'Circle type updated successfully.');
    }

    public function destroy($id)
    {
        $circleType = CircleType::where('id', $id)
            ->where('created_by', createdBy())
            ->first();

        if (!$circleType) {
            return redirect()->back()->with('error', 'Circle type not found.');
        }

        // Check if any court is mapped with this circle type
        $courtsCount = Court::where('circle_type_id', $id)->count();

        if ($courtsCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete circle type. There are ' . $courtsCount . ' court(s) mapped with this circle type.');
        }

        $circleType->delete();

        return redirect()->back()->with('success', 'Circle type deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $circleType = CircleType::where('id', $id)
            ->where('created_by', createdBy())
            ->first();

        if (!$circleType) {
            return redirect()->back()->with('error', 'Circle type not found.');
        }

        $circleType->status = $circleType->status === 'active' ? 'inactive' : 'active';
        $circleType->save();

        return redirect()->back()->with('success', 'Circle type status updated successfully.');
    }
}

