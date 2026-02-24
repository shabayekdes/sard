<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseCategory::withPermissionCheck()
            ->with(['creator']);

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

        // Handle status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'desc');

        if (!empty($sortField)) {
            // Validate sort direction
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'desc';
            }

            // For translatable fields, sort by the current locale
            if (in_array($sortField, ['name', 'description'])) {
                $locale = app()->getLocale();
                $query->orderByRaw("JSON_EXTRACT({$sortField}, '$.{$locale}') {$sortDirection}");
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $expenseCategories = $query->paginate($request->per_page ?? 10);

        // Transform the data to include translated values
        $expenseCategories->getCollection()->transform(function ($expenseCategory) {
            return [
                'id' => $expenseCategory->id,
                'name' => $expenseCategory->name, // Spatie will automatically return translated value for display
                'name_translations' => $expenseCategory->getTranslations('name'), // Full translations for editing
                'description' => $expenseCategory->description, // Spatie will automatically return translated value for display
                'description_translations' => $expenseCategory->getTranslations('description'), // Full translations for editing
                'status' => $expenseCategory->status,
                'tenant_id' => $expenseCategory->tenant_id,
                'creator' => $expenseCategory->creator,
                'created_at' => $expenseCategory->created_at,
                'updated_at' => $expenseCategory->updated_at,
            ];
        });

        return Inertia::render('billing/expense-categories/index', [
            'expenseCategories' => $expenseCategories,
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
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        // Check if expense category with same name already exists for this company
        $exists = ExpenseCategory::where('tenant_id', createdBy())
            ->whereRaw("JSON_EXTRACT(name, '$.ar') = ?", [$validated['name']['ar']])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Expense category with this name already exists.');
        }

        ExpenseCategory::create($validated);

        return redirect()->back()->with('success', 'Expense category created successfully.');
    }

    public function update(Request $request, $expenseCategoryId)
    {
        $expenseCategory = ExpenseCategory::where('id', $expenseCategoryId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($expenseCategory) {
            try {
                $validated = $request->validate([
                    'name' => 'required|array',
                    'name.en' => 'required|string|max:255',
                    'name.ar' => 'required|string|max:255',
                    'description' => 'nullable|array',
                    'description.en' => 'nullable|string',
                    'description.ar' => 'nullable|string',
                    'status' => 'nullable|in:active,inactive',
                ]);

                // Check if expense category with same name already exists for this company (excluding current)
                $exists = ExpenseCategory::where('tenant_id', createdBy())
                    ->whereRaw("JSON_EXTRACT(name, '$.ar') = ?", [$validated['name']['ar']])
                    ->where('id', '!=', $expenseCategoryId)
                    ->exists();

                if ($exists) {
                    return redirect()->back()->with('error', 'Expense category with this name already exists.');
                }

                $expenseCategory->update($validated);

                return redirect()->back()->with('success', 'Expense category updated successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to update expense category');
            }
        } else {
            return redirect()->back()->with('error', 'Expense category not found.');
        }
    }

    public function destroy($expenseCategoryId)
    {
        $expenseCategory = ExpenseCategory::where('id', $expenseCategoryId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($expenseCategory) {
            try {
                $expenseCategory->delete();
                return redirect()->back()->with('success', 'Expense category deleted successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to delete expense category');
            }
        } else {
            return redirect()->back()->with('error', 'Expense category not found.');
        }
    }

    public function toggleStatus($expenseCategoryId)
    {
        $expenseCategory = ExpenseCategory::where('id', $expenseCategoryId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($expenseCategory) {
            try {
                $expenseCategory->status = $expenseCategory->status === 'active' ? 'inactive' : 'active';
                $expenseCategory->save();

                return redirect()->back()->with('success', 'Expense category status updated successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to update expense category status');
            }
        } else {
            return redirect()->back()->with('error', 'Expense category not found.');
        }
    }
}