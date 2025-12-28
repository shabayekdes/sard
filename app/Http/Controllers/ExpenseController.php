<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends BaseController
{
    public function index(Request $request)
    {
        $query = Expense::withPermissionCheck()->with(['category', 'creator', 'case']);

        if ($request->search) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm, $locale) {
                      // Search in translatable name field
                      $categoryQuery->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                          ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                          ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"]);
                  });
            });
        }

        if ($request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->is_billable !== null) {
            $query->where('is_billable', $request->is_billable);
        }

        if ($request->is_approved !== null) {
            $query->where('is_approved', $request->is_approved);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(10);
        
        // Transform expenses to include translated category names
        $expenses->getCollection()->transform(function ($expense) {
            $expenseData = $expense->toArray();
            
            // Add translated category name - ensure it's a string, not an object
            if ($expense->category) {
                $categoryName = $expense->category->name;
                // If it's still an array/object (shouldn't happen with Spatie, but just in case), get the current locale
                if (is_array($categoryName)) {
                    $locale = app()->getLocale();
                    $expenseData['category_name'] = $categoryName[$locale] ?? $categoryName['en'] ?? $categoryName['ar'] ?? '';
                } else {
                    $expenseData['category_name'] = $categoryName;
                }
            }
            
            return $expenseData;
        });
        
        // Get categories with translated names
        $categories = ExpenseCategory::withPermissionCheck()
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name, // Spatie will automatically return translated value
                ];
            });
        
        $cases = \App\Models\CaseModel::withPermissionCheck()->select('id', 'case_id', 'title')->get();

        return Inertia::render('billing/expenses/index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'cases' => $cases,
            'filters' => $request->only(['search', 'category_id', 'is_billable', 'is_approved']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_id' => 'nullable|exists:cases,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'is_billable' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        Expense::create([
            'created_by' => createdBy(),
            'case_id' => $request->case_id,
            'expense_category_id' => $request->expense_category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'is_billable' => $request->boolean('is_billable', true),
            'is_approved' => false,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'case_id' => 'nullable|exists:cases,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'is_billable' => 'required|in:true,false,1,0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $expense->update([
            'case_id' => $request->case_id,
            'expense_category_id' => $request->expense_category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'is_billable' => filter_var($request->is_billable, FILTER_VALIDATE_BOOLEAN),
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    public function approve(Expense $expense)
    {
        $expense->update(['is_approved' => !$expense->is_approved]);
        return redirect()->back()->with('success', 'Expense approval status updated successfully.');
    }
}