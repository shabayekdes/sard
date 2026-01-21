<?php

namespace App\Http\Controllers;

use App\Models\TaxRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxRateController extends Controller
{
    /**
     * Display a listing of tax rates.
     */
    public function index(Request $request)
    {
        $query = TaxRate::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(description, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('rate', 'like', "%{$searchTerm}%");
            });
        }

        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        if (in_array($sortField, ['name', 'description'])) {
            $locale = app()->getLocale();
            $query->orderByRaw("JSON_EXTRACT({$sortField}, '$.{$locale}') {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $perPage = $request->input('per_page', 10);
        $taxRates = $query->paginate($perPage)->withQueryString();
        $taxRates->getCollection()->transform(function ($taxRate) {
            return [
                'id' => $taxRate->id,
                'name' => $taxRate->name,
                'name_translations' => $taxRate->getTranslations('name'),
                'description' => $taxRate->description,
                'description_translations' => $taxRate->getTranslations('description'),
                'rate' => $taxRate->rate,
                'is_active' => $taxRate->is_active,
                'created_at' => $taxRate->created_at,
                'updated_at' => $taxRate->updated_at,
            ];
        });

        return Inertia::render('tax-rates/index', [
            'taxRates' => $taxRates,
            'filters' => $request->all(['search', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Store a newly created tax rate.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        TaxRate::create($validated);

        return redirect()->back();
    }

    /**
     * Update the specified tax rate.
     */
    public function update(Request $request, TaxRate $taxRate)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $taxRate->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified tax rate.
     */
    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return redirect()->back();
    }
}
