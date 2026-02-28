<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientBillingCurrencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Currency::query();

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhere('symbol', 'like', "%{$searchTerm}%");
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
        $currencies = $query->paginate($perPage)->withQueryString();

        return Inertia::render('client-billing-currencies/index', [
            'currencies' => $currencies,
            'filters' => $request->all(['search', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('currencies', 'code'),
            ],
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = true;
        $validated['name'] = ['en' => $validated['name'], 'ar' => $validated['name']];
        if (!empty($validated['description'])) {
            $validated['description'] = ['en' => $validated['description'], 'ar' => $validated['description']];
        }

        Currency::create($validated);

        return redirect()->back()->with('success', 'Currency created successfully.');
    }

    public function update(Request $request, Currency $clientBillingCurrency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('currencies', 'code')
                    ->ignore($clientBillingCurrency->id),
            ],
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        $validated['name'] = ['en' => $validated['name'], 'ar' => $validated['name']];
        if (!empty($validated['description'])) {
            $validated['description'] = ['en' => $validated['description'], 'ar' => $validated['description']];
        }

        $clientBillingCurrency->update($validated);

        return redirect()->back()->with('success', 'Currency updated successfully.');
    }

    public function destroy(Currency $clientBillingCurrency)
    {
        if (Settings::string('DEFAULT_CURRENCY') === $clientBillingCurrency->code) {
            return redirect()->back()->with('error', 'Cannot delete the default currency.');
        }

        $clientBillingCurrency->delete();

        return redirect()->back()->with('success', 'Currency deleted successfully.');
    }

    public function getAllCurrencies()
    {
        $locale = app()->getLocale();
        $currencies = Currency::where('status', true)
            ->orderByRaw("JSON_EXTRACT(name, '$.{$locale}') ASC")
            ->get(['id', 'name', 'code', 'symbol']);
        
        return response()->json($currencies);
    }
}