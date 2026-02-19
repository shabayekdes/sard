<?php

namespace App\Http\Controllers;

use App\Models\ResearchType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ResearchTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchType::withPermissionCheck()
            ->with(['creator'])
            ->where('created_by', createdBy());

        // Handle search - search in code and translatable fields
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->where('code', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        // Handle status filter
        if ($request->has('status') && ! empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'asc');
        if (! empty($sortField)) {
            if (in_array($sortField, ['name', 'description'])) {
                $locale = app()->getLocale();
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT({$sortField}, '$.{$locale}')) " . ($sortDirection === 'desc' ? 'DESC' : 'ASC'));
            } elseif ($sortField === 'code') {
                $query->orderBy('code', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $researchTypes = $query->paginate($request->per_page ?? 10);

        $researchTypes->getCollection()->transform(function ($researchType) {
            return [
                'id' => $researchType->id,
                'code' => $researchType->code,
                'name' => $researchType->name,
                'name_translations' => $researchType->getTranslations('name'),
                'description' => $researchType->description,
                'description_translations' => $researchType->getTranslations('description'),
                'status' => $researchType->status,
                'created_by' => $researchType->created_by,
                'creator' => $researchType->creator,
                'created_at' => $researchType->created_at,
                'updated_at' => $researchType->updated_at,
            ];
        });

        return Inertia::render('legal-research/research-types/index', [
            'researchTypes' => $researchTypes,
            'filters' => $request->all(['search', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|array',
            'name.ar' => 'nullable|string|max:255',
            'name.en' => 'required_with:name|string|max:255',
            'description' => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        $exists = ResearchType::where('created_by', createdBy())->where('code', $validated['code'])->exists();
        if ($exists) {
            return redirect()->back()->with('error', __('Research type with this code already exists.'));
        }

        ResearchType::create($validated);

        return redirect()->back()->with('success', __('Research type created successfully.'));
    }

    public function update(Request $request, $researchTypeId)
    {
        $researchType = ResearchType::where('id', $researchTypeId)
            ->where('created_by', createdBy())
            ->first();

        if ($researchType) {
            try {
                $validated = $request->validate([
                    'code' => 'required|string|max:50',
                    'name' => 'required|array',
                    'name.ar' => 'nullable|string|max:255',
                    'name.en' => 'required_with:name|string|max:255',
                    'description' => 'nullable|array',
                    'description.ar' => 'nullable|string',
                    'description.en' => 'nullable|string',
                    'status' => 'nullable|in:active,inactive',
                ]);

                $exists = ResearchType::where('created_by', createdBy())
                    ->where('id', '!=', $researchTypeId)
                    ->where('code', $validated['code'])
                    ->exists();
                if ($exists) {
                    return redirect()->back()->with('error', __('Research type with this code already exists.'));
                }

                $researchType->update($validated);

                return redirect()->back()->with('success', __('Research type updated successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to update research type.'));
            }
        } else {
            return redirect()->back()->with('error', __('Research type not found.'));
        }
    }

    public function destroy($researchTypeId)
    {
        $researchType = ResearchType::where('id', $researchTypeId)
            ->where('created_by', createdBy())
            ->first();

        if ($researchType) {
            try {
                // Check if research type has research projects
                $projectCount = $researchType->researchProjects()->count();
                if ($projectCount > 0) {
                    return response()->json(['message' => __('Cannot delete research type with assigned research projects.')], 400);
                }
                
                $researchType->delete();
                return redirect()->back()->with('success', __('Research type deleted successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to delete research type.'));
            }
        } else {
            return redirect()->back()->with('error', __('Research type not found.'));
        }
    }

    public function toggleStatus($researchTypeId)
    {
        $researchType = ResearchType::where('id', $researchTypeId)
            ->where('created_by', createdBy())
            ->first();

        if ($researchType) {
            try {
                $researchType->status = $researchType->status === 'active' ? 'inactive' : 'active';
                $researchType->save();

                return redirect()->back()->with('success', __('Research type status updated successfully.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to update research type status.'));
            }
        } else {
            return redirect()->back()->with('error', __('Research type not found.'));
        }
    }
}