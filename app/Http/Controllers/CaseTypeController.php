<?php

namespace App\Http\Controllers;

use App\Models\CaseType;
use App\Models\CaseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaseTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseType::withPermissionCheck()
            ->with(['creator', 'caseCategory.parent'])
            ->where('created_by', createdBy());

        // Handle search - search in translatable fields
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                // Search in JSON translatable fields
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

        $caseTypes = $query->paginate($request->per_page ?? 10);
        
        // Transform the data to include translated values
        $caseTypes->getCollection()->transform(function ($caseType) {
            return [
                'id' => $caseType->id,
                'name' => $caseType->name, // Spatie will automatically return translated value for display
                'name_translations' => $caseType->getTranslations('name'), // Full translations for editing
                'description' => $caseType->description, // Spatie will automatically return translated value for display
                'description_translations' => $caseType->getTranslations('description'), // Full translations for editing
                'case_category_id' => $caseType->case_category_id,
                'caseCategory' => $caseType->caseCategory ? [
                    'id' => $caseType->caseCategory->id,
                    'name' => $caseType->caseCategory->name,
                    'name_translations' => $caseType->caseCategory->getTranslations('name'),
                    'parent_id' => $caseType->caseCategory->parent_id,
                    'parent' => $caseType->caseCategory->parent ? [
                        'id' => $caseType->caseCategory->parent->id,
                        'name' => $caseType->caseCategory->parent->name,
                        'name_translations' => $caseType->caseCategory->parent->getTranslations('name'),
                    ] : null,
                ] : null,
                'color' => $caseType->color,
                'status' => $caseType->status,
                'created_by' => $caseType->created_by,
                'creator' => $caseType->creator,
                'created_at' => $caseType->created_at,
                'updated_at' => $caseType->updated_at,
            ];
        });
        
        // Get case categories for dropdown
        $caseCategories = CaseCategory::where('created_by', createdBy())
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->get(['id', 'name']);

        // Transform case categories for dropdown
        $caseCategories->transform(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name, // Will be translated by Spatie
                'name_translations' => $category->getTranslations('name'),
            ];
        });

        return Inertia::render('cases/case-types/index', [
            'caseTypes' => $caseTypes,
            'caseCategories' => $caseCategories,
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
            'case_category_id' => 'required|exists:case_categories,id',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Validate that category is a subcategory (has parent) and belongs to the same user
        $category = CaseCategory::where('id', $validated['case_category_id'])
            ->where('created_by', createdBy())
            ->first();
        
        if (!$category) {
            return redirect()->back()->with('error', 'Invalid case category selected.');
        }

        // Ensure it's a subcategory (has a parent)
        if (!$category->parent_id) {
            return redirect()->back()->with('error', 'Please select a subcategory (child category), not a parent category.');
        }

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['color'] = $validated['color'] ?? '#3B82F6';

        CaseType::create($validated);

        return redirect()->back()->with('success', 'Case type created successfully.');
    }

    public function update(Request $request, $caseTypeId)
    {
        $caseType = CaseType::where('id', $caseTypeId)->where('created_by', createdBy())->first();

        if (!$caseType) {
            return redirect()->back()->with('error', 'Case type not found.');
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'case_category_id' => 'required|exists:case_categories,id',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Validate that category is a subcategory (has parent) and belongs to the same user
        $category = CaseCategory::where('id', $validated['case_category_id'])
            ->where('created_by', createdBy())
            ->first();
        
        if (!$category) {
            return redirect()->back()->with('error', 'Invalid case category selected.');
        }

        // Ensure it's a subcategory (has a parent)
        if (!$category->parent_id) {
            return redirect()->back()->with('error', 'Please select a subcategory (child category), not a parent category.');
        }

        $validated['color'] = $validated['color'] ?? '#3B82F6';

        $caseType->update($validated);

        return redirect()->back()->with('success', 'Case type updated successfully.');
    }

    public function destroy($caseTypeId)
    {
        $caseType = CaseType::where('id', $caseTypeId)->where('created_by', createdBy())->first();

        if (!$caseType) {
            return redirect()->back()->with('error', 'Case type not found.');
        }

        if ($caseType->cases()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete case type that has associated cases.');
        }

        $caseType->delete();

        return redirect()->back()->with('success', 'Case type deleted successfully.');
    }

    public function toggleStatus($caseTypeId)
    {
        $caseType = CaseType::where('id', $caseTypeId)->where('created_by', createdBy())->first();

        if (!$caseType) {
            return redirect()->back()->with('error', 'Case type not found.');
        }

        $caseType->status = $caseType->status === 'active' ? 'inactive' : 'active';
        $caseType->save();

        return redirect()->back()->with('success', 'Case type status updated successfully.');
    }
}