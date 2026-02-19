<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentCategory::withPermissionCheck()
            ->where('created_by', createdBy());

        if ($request->has('search') && ! empty($request->search)) {
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

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'asc');
        if (! empty($sortField)) {
            if (! in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'asc';
            }
            if (in_array($sortField, ['name', 'description'])) {
                $locale = app()->getLocale();
                $query->orderByRaw("JSON_EXTRACT({$sortField}, '$.{$locale}') {$sortDirection}");
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $categories = $query->paginate($request->per_page ?? 10);

        $categories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'category_id' => $category->category_id,
                'name' => $category->name,
                'name_translations' => $category->getTranslations('name'),
                'description' => $category->description,
                'description_translations' => $category->getTranslations('description'),
                'color' => $category->color,
                'status' => $category->status,
                'created_by' => $category->created_by,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ];
        });

        return Inertia::render('document-management/categories/index', [
            'categories' => $categories,
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

        DocumentCategory::create($validated);

        return redirect()->back()->with('success', 'Document category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = DocumentCategory::where('id', $id)->where('created_by', createdBy())->first();

        if (!$category) {
            return redirect()->back()->with('error', 'Document category not found.');
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

        $category->update($validated);

        return redirect()->back()->with('success', 'Document category updated successfully.');
    }

    public function destroy($id)
    {
        $category = DocumentCategory::where('id', $id)->where('created_by', createdBy())->first();

        if (!$category) {
            return redirect()->back()->with('error', 'Document category not found.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Document category deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $category = DocumentCategory::where('id', $id)->where('created_by', createdBy())->first();

        if (!$category) {
            return redirect()->back()->with('error', 'Document category not found.');
        }

        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();

        return redirect()->back()->with('success', 'Document category status updated successfully.');
    }
}