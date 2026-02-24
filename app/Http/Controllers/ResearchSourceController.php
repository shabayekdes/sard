<?php

namespace App\Http\Controllers;

use App\Enum\ResearchSourceType;
use App\Models\ResearchSource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResearchSourceController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchSource::withPermissionCheck()
            ->with(['creator'])
            ->where('tenant_id', createdBy());

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $locale = app()->getLocale();
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->where('url', 'like', '%' . $searchTerm . '%')
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(source_name, '$.{$locale}')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(source_name, '$.en')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(source_name, '$.ar')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$searchTerm}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        if ($request->has('source_type') && $request->source_type !== 'all') {
            $query->where('source_type', $request->source_type);
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'asc');
        if (!empty($sortField)) {
            if (in_array($sortField, ['source_name', 'description'])) {
                $locale = app()->getLocale();
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT({$sortField}, '$.{$locale}')) " . ($sortDirection === 'desc' ? 'DESC' : 'ASC'));
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $locale = app()->getLocale();
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(source_name, '$.{$locale}')) asc");
        }

        $sources = $query->paginate($request->per_page ?? 10);

        $sources->getCollection()->transform(function ($source) {
            return [
                'id' => $source->id,
                'source_name' => $source->source_name,
                'source_name_translations' => $source->getTranslations('source_name'),
                'description' => $source->description,
                'description_translations' => $source->getTranslations('description'),
                'source_type' => $source->source_type?->value ?? $source->source_type,
                'url' => $source->url,
                'access_info' => $source->access_info,
                'credentials' => $source->credentials,
                'status' => $source->status,
                'tenant_id' => $source->tenant_id,
                'creator' => $source->creator,
                'created_at' => $source->created_at,
                'updated_at' => $source->updated_at,
            ];
        });

        return Inertia::render('legal-research/sources/index', [
            'sources' => $sources,
            'filters' => $request->all(['search', 'source_type', 'status', 'sort_field', 'sort_direction', 'per_page']),
            'sourceTypeOptions' => ResearchSourceType::optionsForFrontend(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_name' => 'required|array',
            'source_name.ar' => 'nullable|string|max:255',
            'source_name.en' => 'required_with:source_name|string|max:255',
            'source_type' => 'required|string|in:' . implode(',', array_column(ResearchSourceType::cases(), 'value')),
            'description' => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'url' => 'nullable|url',
            'access_info' => 'nullable|string',
            'credentials' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        ResearchSource::create($validated);

        return redirect()->back()->with('success', 'Research source created successfully.');
    }

    public function update(Request $request, $sourceId)
    {
        $source = ResearchSource::where('id', $sourceId)->where('tenant_id', createdBy())->first();

        if (!$source) {
            return redirect()->back()->with('error', 'Research source not found.');
        }

        $validated = $request->validate([
            'source_name' => 'required|array',
            'source_name.ar' => 'nullable|string|max:255',
            'source_name.en' => 'required_with:source_name|string|max:255',
            'source_type' => 'required|string|in:' . implode(',', array_column(ResearchSourceType::cases(), 'value')),
            'description' => 'nullable|array',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'url' => 'nullable|url',
            'access_info' => 'nullable|string',
            'credentials' => 'nullable|array',
            'status' => 'nullable|in:active,inactive',
        ]);

        $source->update($validated);

        return redirect()->back()->with('success', 'Research source updated successfully.');
    }

    public function destroy($sourceId)
    {
        $source = ResearchSource::where('id', $sourceId)->where('tenant_id', createdBy())->first();

        if (!$source) {
            return redirect()->back()->with('error', 'Research source not found.');
        }

        $source->delete();

        return redirect()->back()->with('success', 'Research source deleted successfully.');
    }

    public function toggleStatus($sourceId)
    {
        $source = ResearchSource::where('id', $sourceId)->where('tenant_id', createdBy())->first();

        if (!$source) {
            return redirect()->back()->with('error', 'Research source not found.');
        }

        $source->status = $source->status === 'active' ? 'inactive' : 'active';
        $source->save();

        return redirect()->back()->with('success', 'Research source status updated successfully.');
    }
}