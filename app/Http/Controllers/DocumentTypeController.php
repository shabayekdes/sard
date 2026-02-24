<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentType::withPermissionCheck()
            ->with(['creator'])
            ->where('tenant_id', createdBy());

        // Handle search - search in translatable fields
        if ($request->has('search') && ! empty($request->search)) {
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

        if ($request->has('status') && ! empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Only apply sorting if sort_field is provided and valid
        if (! empty($sortField)) {
            // Validate sort direction
            if (! in_array($sortDirection, ['asc', 'desc'])) {
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
            // Default sorting when no sort field is provided
            $query->orderBy('created_at', 'desc');
        }

        $documentTypes = $query->paginate($request->per_page ?? 10);

        // Transform the data to include translated values
        $documentTypes->getCollection()->transform(function ($documentType) {
            return [
                'id' => $documentType->id,
                'name' => $documentType->name, // Spatie will automatically return translated value for display
                'name_translations' => $documentType->getTranslations('name'), // Full translations for editing
                'description' => $documentType->description, // Spatie will automatically return translated value for display
                'description_translations' => $documentType->getTranslations('description'), // Full translations for editing
                'color' => $documentType->color,
                'status' => $documentType->status,
                'tenant_id' => $documentType->tenant_id,
                'creator' => $documentType->creator,
                'created_at' => $documentType->created_at,
                'updated_at' => $documentType->updated_at,
            ];
        });

        return Inertia::render('advocate/document-types/index', [
            'documentTypes' => $documentTypes,
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

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        DocumentType::create($validated);

        return redirect()->back()->with('success', 'Document type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $documentType = DocumentType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (! $documentType) {
            return redirect()->back()->with('error', 'Document type not found.');
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

        $documentType->update($validated);

        return redirect()->back()->with('success', 'Document type updated successfully.');
    }

    public function destroy($id)
    {
        $documentType = DocumentType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (! $documentType) {
            return redirect()->back()->with('error', 'Document type not found.');
        }

        $documentType->delete();

        return redirect()->back()->with('success', 'Document type deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $documentType = DocumentType::where('id', $id)
            ->where('tenant_id', createdBy())
            ->first();

        if (! $documentType) {
            return redirect()->back()->with('error', 'Document type not found.');
        }

        $documentType->status = $documentType->status === 'active' ? 'inactive' : 'active';
        $documentType->save();

        return redirect()->back()->with('success', 'Document type status updated successfully.');
    }
}
