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
        
        // Transform expenses to include translated category names and convert receipt_file array to comma-separated string
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
            
            // Convert receipt_file array to comma-separated string for frontend MediaPicker
            if (isset($expenseData['receipt_file']) && is_array($expenseData['receipt_file'])) {
                $expenseData['receipt_file'] = implode(',', array_filter($expenseData['receipt_file']));
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
            'receipt_file' => 'nullable',
        ]);

        // Convert receipt_file from comma-separated string to array and extract storage paths
        $receiptFiles = null;
        if ($request->has('receipt_file') && $request->receipt_file) {
            if (is_string($request->receipt_file)) {
                $receiptFiles = array_filter(array_map('trim', explode(',', $request->receipt_file)));
            } elseif (is_array($request->receipt_file)) {
                $receiptFiles = array_filter($request->receipt_file);
            }
            
            // Extract storage path from full URLs (store only path from /storage/ onwards)
            if ($receiptFiles) {
                $receiptFiles = array_map(function ($file) {
                    return $this->extractStoragePath($file);
                }, $receiptFiles);
            }
            
            // Convert empty array to null
            if (empty($receiptFiles)) {
                $receiptFiles = null;
            }
        }

        Expense::create([
            'tenant_id' => createdBy(),
            'case_id' => $request->case_id,
            'expense_category_id' => $request->expense_category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'is_billable' => $request->boolean('is_billable', true),
            'is_approved' => false,
            'notes' => $request->notes,
            'receipt_file' => $receiptFiles,
        ]);

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Expense')]));
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
            'receipt_file' => 'nullable',
        ]);

        // Convert receipt_file from comma-separated string to array and extract storage paths
        $receiptFiles = null;
        if ($request->has('receipt_file') && $request->receipt_file) {
            if (is_string($request->receipt_file)) {
                $receiptFiles = array_filter(array_map('trim', explode(',', $request->receipt_file)));
            } elseif (is_array($request->receipt_file)) {
                $receiptFiles = array_filter($request->receipt_file);
            }
            
            // Extract storage path from full URLs (store only path from /storage/ onwards)
            if ($receiptFiles) {
                $receiptFiles = array_map(function ($file) {
                    return $this->extractStoragePath($file);
                }, $receiptFiles);
            }
            
            // Convert empty array to null
            if (empty($receiptFiles)) {
                $receiptFiles = null;
            }
        }

        $expense->update([
            'case_id' => $request->case_id,
            'expense_category_id' => $request->expense_category_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'is_billable' => filter_var($request->is_billable, FILTER_VALIDATE_BOOLEAN),
            'notes' => $request->notes,
            'receipt_file' => $receiptFiles,
        ]);

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Expense')]));
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Expense')]));
    }

    public function approve(Expense $expense)
    {
        $expense->update(['is_approved' => !$expense->is_approved]);
        return redirect()->back()->with('success', __('Expense approval status updated successfully.'));
    }

    /**
     * Extract storage path from full URL or return path as is
     * Converts full URLs like "https://example.com/storage/app/public/files/image.jpg"
     * to "/storage/app/public/files/image.jpg" or "storage/app/public/files/image.jpg"
     * 
     * @param string $filePath
     * @return string
     */
    private function extractStoragePath($filePath)
    {
        if (empty($filePath)) {
            return $filePath;
        }

        // If it's already a relative path starting with /storage/, return as is
        if (str_starts_with($filePath, '/storage/')) {
            return $filePath;
        }

        // If it's a relative path starting with storage/ (without leading slash), return as is
        if (str_starts_with($filePath, 'storage/')) {
            return '/' . $filePath;
        }

        // If it's a full URL, extract the path from /storage/ onwards
        if (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://')) {
            $storageIndex = strpos($filePath, '/storage/');
            if ($storageIndex !== false) {
                return substr($filePath, $storageIndex);
            }
        }

        // If it doesn't contain /storage/, return as is (might be a different path format)
        return $filePath;
    }
}