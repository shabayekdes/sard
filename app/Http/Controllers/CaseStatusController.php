<?php

namespace App\Http\Controllers;

use App\Models\CaseStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaseStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseStatus::withPermissionCheck()
            ->with(['creator'])
            ->where('created_by', createdBy());

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

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $caseStatuses = $query->paginate($request->per_page ?? 10);

        // Transform the data to include translated values
        $caseStatuses->getCollection()->transform(function ($caseStatus) {
            return [
                'id' => $caseStatus->id,
                'name' => $caseStatus->name,
                'name_translations' => $caseStatus->getTranslations('name'),
                'description' => $caseStatus->description,
                'description_translations' => $caseStatus->getTranslations('description'),
                'color' => $caseStatus->color,
                'is_default' => $caseStatus->is_default,
                'is_closed' => $caseStatus->is_closed,
                'status' => $caseStatus->status,
                'created_by' => $caseStatus->created_by,
                'creator' => $caseStatus->creator,
                'created_at' => $caseStatus->created_at,
                'updated_at' => $caseStatus->updated_at,
            ];
        });

        return Inertia::render('cases/case-statuses/index', [
            'caseStatuses' => $caseStatuses,
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
            'color' => 'nullable|string|max:7',
            'is_default' => 'nullable|boolean',
            'is_closed' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['color'] = $validated['color'] ?? '#10B981';
        $validated['is_default'] = $validated['is_default'] ?? false;
        $validated['is_closed'] = $validated['is_closed'] ?? false;

        // If setting as default, remove default from others
        if ($validated['is_default']) {
            CaseStatus::where('created_by', createdBy())->update(['is_default' => false]);
        }

        CaseStatus::create($validated);

        return redirect()->back()->with('success', 'Case status created successfully.');
    }

    public function update(Request $request, $caseStatusId)
    {
        $caseStatus = CaseStatus::where('id', $caseStatusId)->where('created_by', createdBy())->first();

        if (!$caseStatus) {
            return redirect()->back()->with('error', 'Case status not found.');
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_default' => 'nullable|boolean',
            'is_closed' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['color'] = $validated['color'] ?? '#10B981';
        $validated['is_default'] = $validated['is_default'] ?? false;
        $validated['is_closed'] = $validated['is_closed'] ?? false;

        // If setting as default, remove default from others
        if ($validated['is_default']) {
            CaseStatus::where('created_by', createdBy())->where('id', '!=', $caseStatusId)->update(['is_default' => false]);
        }

        $caseStatus->update($validated);

        return redirect()->back()->with('success', 'Case status updated successfully.');
    }

    public function destroy($caseStatusId)
    {
        $caseStatus = CaseStatus::where('id', $caseStatusId)->where('created_by', createdBy())->first();

        if (!$caseStatus) {
            return redirect()->back()->with('error', 'Case status not found.');
        }

        if ($caseStatus->cases()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete case status that has associated cases.');
        }

        $caseStatus->delete();

        return redirect()->back()->with('success', 'Case status deleted successfully.');
    }

    public function toggleStatus($caseStatusId)
    {
        $caseStatus = CaseStatus::where('id', $caseStatusId)->where('created_by', createdBy())->first();

        if (!$caseStatus) {
            return redirect()->back()->with('error', 'Case status not found.');
        }

        $caseStatus->status = $caseStatus->status === 'active' ? 'inactive' : 'active';
        $caseStatus->save();

        return redirect()->back()->with('success', 'Case status updated successfully.');
    }
}