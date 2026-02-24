<?php

namespace App\Http\Controllers;

use App\Models\ClientType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientType::withPermissionCheck()
            ->with(['creator']);

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

        $clientTypes = $query->paginate($request->per_page ?? 10);

        // Transform the data to include translated values
        $clientTypes->getCollection()->transform(function ($clientType) {
            return [
                'id' => $clientType->id,
                'name' => $clientType->name, // Spatie will automatically return translated value for display
                'name_translations' => $clientType->getTranslations('name'), // Full translations for editing
                'description' => $clientType->description, // Spatie will automatically return translated value for display
                'description_translations' => $clientType->getTranslations('description'), // Full translations for editing
                'status' => $clientType->status,
                'tenant_id' => $clientType->tenant_id,
                'creator' => $clientType->creator,
                'created_at' => $clientType->created_at,
                'updated_at' => $clientType->updated_at,
            ];
        });

        return Inertia::render('clients/client-types/index', [
            'clientTypes' => $clientTypes,
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

        // Check if client type with same name already exists for this company
        $exists = ClientType::where('tenant_id', createdBy())
            ->whereRaw("JSON_EXTRACT(name, '$.ar') = ?", [$validated['name']['ar']])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Client type with this name already exists.');
        }

        ClientType::create($validated);

        return redirect()->back()->with('success', 'Client type created successfully.');
    }

    public function update(Request $request, $clientTypeId)
    {
        $clientType = ClientType::where('id', $clientTypeId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($clientType) {
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

                // Check if client type with same name already exists for this company (excluding current)
                $exists = ClientType::where('tenant_id', createdBy())
                    ->whereRaw("JSON_EXTRACT(name, '$.ar') = ?", [$validated['name']['ar']])
                    ->where('id', '!=', $clientTypeId)
                    ->exists();

                if ($exists) {
                    return redirect()->back()->with('error', 'Client type with this name already exists.');
                }

                $clientType->update($validated);

                return redirect()->back()->with('success', 'Client type updated successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to update client type');
            }
        } else {
            return redirect()->back()->with('error', 'Client type not found.');
        }
    }

    public function destroy($clientTypeId)
    {
        $clientType = ClientType::where('id', $clientTypeId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($clientType) {
            try {
                // Check if client type has clients
                if (class_exists('App\\Models\\Client')) {
                    $clientCount = \App\Models\Client::where('client_type_id', $clientTypeId)->count();
                    if ($clientCount > 0) {
                        return response()->json(['message' => 'Cannot delete client type with assigned clients'], 400);
                    }
                }
                $clientType->delete();
                return redirect()->back()->with('success', 'Client type deleted successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to delete client type');
            }
        } else {
            return redirect()->back()->with('error', 'Client type not found.');
        }
    }

    public function toggleStatus($clientTypeId)
    {
        $clientType = ClientType::where('id', $clientTypeId)
            ->where('tenant_id', createdBy())
            ->first();

        if ($clientType) {
            try {
                $clientType->status = $clientType->status === 'active' ? 'inactive' : 'active';
                $clientType->save();

                return redirect()->back()->with('success', 'Client type status updated successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to update client type status');
            }
        } else {
            return redirect()->back()->with('error', 'Client type not found.');
        }
    }
}