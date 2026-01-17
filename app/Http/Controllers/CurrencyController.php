<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(Request $request)
    {
        $query = Currency::query()->whereNull('created_by');

        // Handle search - search in translatable fields
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                // Search in JSON translatable fields
                $q->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhere('symbol', 'like', "%{$searchTerm}%");
            });
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // For translatable fields, sort by the current locale
        if (in_array($sortField, ['name', 'description'])) {
            $locale = app()->getLocale();
            $query->orderByRaw("JSON_EXTRACT({$sortField}, '$.{$locale}') {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $currencies = $query->paginate($perPage)->withQueryString();

        // Transform the data using CurrencyResource while preserving pagination structure
        $currencies->through(function ($currency) {
            return (new CurrencyResource($currency))->resolve();
        });

        return Inertia::render('currencies/index', [
            'currencies' => $currencies,
            'filters' => $request->all(['search', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Store a newly created currency.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('currencies', 'code')->whereNull('created_by'),
            ],
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
        ]);

        $validated['created_by'] = null;
        $validated['status'] = true;

        Currency::create($validated);

        return redirect()->back();
    }

    /**
     * Update the specified currency.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('currencies', 'code')->whereNull('created_by')->ignore($currency->id),
            ],
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
        ]);

        $currency->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified currency.
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->back();
    }

    /**
     * Get all currencies for settings page.
     */
    public function getAllCurrencies()
    {
        $currencies = Currency::whereNull('created_by')->get();

        return response()->json(CurrencyResource::collection($currencies)->resolve());
    }
}
