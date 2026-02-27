<?php

namespace App\Http\Controllers;

use App\Models\CaseCategory;
use App\Models\CaseType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseCategory::withPermissionCheck()
            ->with(['creator', 'parent'])
            ->where('tenant_id', createdBy());

        // Handle search - search in JSON fields
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ['%' . $searchTerm . '%'])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ['%' . $searchTerm . '%'])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ['%' . $searchTerm . '%'])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ['%' . $searchTerm . '%']);
            });
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->latest('id');
        }

        $caseCategories = $query->paginate($request->per_page ?? 10);

        // Transform the data to include translated values
        $caseCategories->getCollection()->transform(function ($caseCategory) {
            return [
                'id' => $caseCategory->id,
                'name' => $caseCategory->name, // Spatie will automatically return translated value for display
                'name_translations' => $caseCategory->getTranslations('name'), // Full translations for editing
                'description' => $caseCategory->description, // Spatie will automatically return translated value for display
                'description_translations' => $caseCategory->getTranslations('description'), // Full translations for editing
                'parent_id' => $caseCategory->parent_id,
                'parent' => $caseCategory->parent ? [
                    'id' => $caseCategory->parent->id,
                    'name' => $caseCategory->parent->name,
                    'name_translations' => $caseCategory->parent->getTranslations('name'),
                ] : null,
                'color' => $caseCategory->color,
                'status' => $caseCategory->status,
                'tenant_id' => $caseCategory->tenant_id,
                'creator' => $caseCategory->creator,
                'created_at' => $caseCategory->created_at,
                'updated_at' => $caseCategory->updated_at,
            ];
        });
        
        // Get all categories for parent dropdown (excluding current item when editing)
        $locale = app()->getLocale();
        $parentCategories = CaseCategory::where('tenant_id', createdBy())
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->orderByRaw("JSON_EXTRACT(name, '$.{$locale}')")
            ->get(['id', 'name']);

        // Transform parent categories for dropdown
        $parentCategories->transform(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name, // Will be translated by Spatie
                'name_translations' => $category->getTranslations('name'),
            ];
        });

        return Inertia::render('cases/case-categories/index', [
            'caseCategories' => $caseCategories,
            'parentCategories' => $parentCategories,
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
            'parent_id' => 'nullable|exists:case_categories,id',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Validate that parent belongs to the same user
        if ($validated['parent_id'] ?? null) {
            $parent = CaseCategory::where('id', $validated['parent_id'])
                ->where('tenant_id', createdBy())
                ->first();
            
            if (!$parent) {
                return redirect()->back()->with('error', __('Invalid parent category selected.'));
            }
        }

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['color'] = $validated['color'] ?? '#3B82F6';

        CaseCategory::create($validated);

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Case category')]));
    }

    public function update(Request $request, $caseCategoryId)
    {
        $caseCategory = CaseCategory::where('id', $caseCategoryId)->where('tenant_id', createdBy())->first();

        if (!$caseCategory) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Case category')]));
        }

        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'parent_id' => 'nullable|exists:case_categories,id',
            'color' => 'nullable|string|max:7',
            'status' => 'nullable|in:active,inactive',
        ]);

        // Prevent setting itself as parent
        if ($validated['parent_id'] == $caseCategoryId) {
            return redirect()->back()->with('error', __('A category cannot be its own parent.'));
        }

        // Validate that parent belongs to the same user
        if ($validated['parent_id'] ?? null) {
            $parent = CaseCategory::where('id', $validated['parent_id'])
                ->where('tenant_id', createdBy())
                ->first();
            
            if (!$parent) {
                return redirect()->back()->with('error', __('Invalid parent category selected.'));
            }

            // Prevent circular references - check if parent is a descendant
            $descendantIds = $this->getDescendantIds($caseCategory);
            if (in_array($validated['parent_id'], $descendantIds)) {
                return redirect()->back()->with('error', __('Cannot set a descendant category as parent.'));
            }
        }

        $validated['color'] = $validated['color'] ?? '#3B82F6';

        $caseCategory->update($validated);

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Case category')]));
    }

    public function destroy($caseCategoryId)
    {
        $caseCategory = CaseCategory::where('id', $caseCategoryId)->where('tenant_id', createdBy())->first();

        if (!$caseCategory) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Case category')]));
        }

        // Check if there are any cases mapped with status (active cases)
        $casesWithStatus = $caseCategory->cases()->where('status', 'active')->count();
        if ($casesWithStatus > 0) {
            return redirect()->back()->with('error', __('Cannot delete case category that has associated cases with active status.'));
        }

        // Check if it has children
        if ($caseCategory->children()->count() > 0) {
            return redirect()->back()->with('error', __('Cannot delete case category that has child categories. Please delete or reassign child categories first.'));
        }

        $caseCategory->delete();

        return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Case category')]));
    }

    public function toggleStatus($caseCategoryId)
    {
        $caseCategory = CaseCategory::where('id', $caseCategoryId)->where('tenant_id', createdBy())->first();

        if (!$caseCategory) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Case category')]));
        }

        $caseCategory->status = $caseCategory->status === 'active' ? 'inactive' : 'active';
        $caseCategory->save();

        return redirect()->back()->with('success', __(':model status updated successfully', ['model' => __('Case category')]));
    }

    /**
     * Get all descendant IDs for a category to prevent circular references
     */
    private function getDescendantIds(CaseCategory $category, $ids = [])
    {
        $children = $category->children;
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = $this->getDescendantIds($child, $ids);
        }
        return $ids;
    }

    /**
     * Get subcategories (children) of a category
     */
    public function getSubcategories(Request $request, $categoryId)
    {
        $category = CaseCategory::where('id', $categoryId)
            ->where('tenant_id', createdBy())
            ->first();

        if (!$category) {
            return response()->json([]);
        }

        $subcategories = CaseCategory::where('parent_id', $categoryId)
            ->where('tenant_id', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        // Transform for frontend
        $locale = app()->getLocale();
        $subcategories->transform(function ($subcategory) use ($locale) {
            return [
                'id' => $subcategory->id,
                'name' => $subcategory->name, // Spatie will automatically return translated value
                'name_translations' => $subcategory->getTranslations('name'),
            ];
        });

        return response()->json($subcategories);
    }

    /**
     * Get case types for a subcategory (case_category_id on case_types = subcategory id)
     */
    public function getCaseTypes(Request $request, $subcategoryId)
    {
        $caseTypes = CaseType::where('case_category_id', $subcategoryId)
            ->where('tenant_id', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $caseTypes->transform(function ($caseType) {
            return [
                'id' => $caseType->id,
                'name' => $caseType->name,
                'name_translations' => $caseType->getTranslations('name'),
            ];
        });

        return response()->json($caseTypes);
    }
}

