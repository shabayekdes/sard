<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CountryController extends Controller
{
    /**
     * Display a listing of countries.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Handle search - search in translatable fields
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                // Search in JSON translatable fields
                $q->whereRaw("JSON_EXTRACT(name, '$.{$locale}') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // For translatable fields, sort by the current locale
        if (in_array($sortField, ['name'])) {
            $locale = app()->getLocale();
            $query->orderByRaw("JSON_EXTRACT({$sortField}, '$.{$locale}') {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $countries = $query->paginate($perPage)->withQueryString();

        // Transform the data to include translated values
        $countries->getCollection()->transform(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name, // Spatie will automatically return translated value for display
                'name_translations' => $country->getTranslations('name'), // Full translations for editing
                'is_active' => $country->is_active,
                'created_at' => $country->created_at,
                'updated_at' => $country->updated_at,
            ];
        });

        return Inertia::render('countries/index', [
            'countries' => $countries,
            'filters' => $request->all(['search', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    /**
     * Store a newly created country.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Country::create($validated);

        return redirect()->back();
    }

    /**
     * Update the specified country.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $country->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified country.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->back();
    }
}
