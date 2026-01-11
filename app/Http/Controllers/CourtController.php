<?php

namespace App\Http\Controllers;

use App\Events\NewCourtCreated;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $query = Court::withPermissionCheck()
            ->with(['creator', 'courtType', 'circleType'])
            ->where('created_by', createdBy());

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('court_id', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        // Handle court type filter
        if ($request->has('court_type_id') && !empty($request->court_type_id) && $request->court_type_id !== 'all') {
            $query->where('court_type_id', $request->court_type_id);
        }

        // Handle circle type filter
        if ($request->has('circle_type_id') && !empty($request->circle_type_id) && $request->circle_type_id !== 'all') {
            $query->where('circle_type_id', $request->circle_type_id);
        }

        // Handle status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $courts = $query->paginate($request->per_page ?? 10);
        
        // Transform the data to include translated values for court types and circle types
        $courts->getCollection()->transform(function ($court) {
            $courtData = $court->toArray();
            if ($court->courtType) {
                $courtData['court_type'] = [
                    'id' => $court->courtType->id,
                    'name' => $court->courtType->name, // Spatie will automatically return translated value
                    'name_translations' => $court->courtType->getTranslations('name'), // Full translations
                    'color' => $court->courtType->color,
                ];
            }
            if ($court->circleType) {
                $courtData['circle_type'] = [
                    'id' => $court->circleType->id,
                    'name' => $court->circleType->name, // Spatie will automatically return translated value
                    'name_translations' => $court->circleType->getTranslations('name'), // Full translations
                    'color' => $court->circleType->color,
                ];
            }
            return $courtData;
        });

        // Get court types for dropdown
        $courtTypes = \App\Models\CourtType::where(function($q) {
                $q->where('created_by', createdBy());
            })
            ->where('status', 'active')
            ->get(['id', 'name', 'color'])
            ->map(function ($courtType) {
                return [
                    'id' => $courtType->id,
                    'name' => $courtType->name, // Spatie will automatically return translated value
                    'name_translations' => $courtType->getTranslations('name'), // Full translations
                    'color' => $courtType->color,
                ];
            });

        // Get circle types for dropdown
        $circleTypes = \App\Models\CircleType::where(function($q) {
                $q->where('created_by', createdBy());
            })
            ->where('status', 'active')
            ->get(['id', 'name', 'color'])
            ->map(function ($circleType) {
                return [
                    'id' => $circleType->id,
                    'name' => $circleType->name, // Spatie will automatically return translated value
                    'name_translations' => $circleType->getTranslations('name'), // Full translations
                    'color' => $circleType->color,
                ];
            });

        return Inertia::render('courts/index', [
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'circleTypes' => $circleTypes,
            'filters' => $request->all(['search', 'court_type_id', 'circle_type_id', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'court_type_id' => 'required|exists:court_types,id',
            'circle_type_id' => 'required|exists:circle_types,id',
            'status' => 'nullable|in:active,inactive',
            'facilities' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        // Check if court with same name already exists for this company
        $exists = Court::where('name', $validated['name'])
            ->where('created_by', createdBy())
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Court with this name already exists.');
        }

        $court = Court::create($validated);

        // Trigger notifications
        if ($court && !IsDemo()) {
            event(new \App\Events\NewCourtCreated($court, $request->all()));
        }

        // Check for errors and combine them
        $emailError = session()->pull('email_error');
        $slackError = session()->pull('slack_error');

        $errors = [];
        if ($emailError) {
            $errors[] = __('Email send failed: ') . $emailError;
        }
        if ($slackError) {
            $errors[] = __('SMS send failed: ') . $slackError;
        }

        if (!empty($errors)) {
            $message = __('Court created successfully, but ') . implode(', ', $errors);
            return redirect()->back()->with('warning', $message);
        }

        return redirect()->back()->with('success', 'Court created successfully.');
    }

    public function update(Request $request, $courtId)
    {
        $court = Court::where('id', $courtId)
            ->where('created_by', createdBy())
            ->first();

        if ($court) {
            try {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'address' => 'nullable|string',
                    'court_type_id' => 'required|exists:court_types,id',
                    'circle_type_id' => 'required|exists:circle_types,id',
                    'status' => 'nullable|in:active,inactive',
                    'facilities' => 'nullable|array',
                    'notes' => 'nullable|string',
                ]);

                // Check if court with same name already exists for this company (excluding current)
                $exists = Court::where('name', $validated['name'])
                    ->where('created_by', createdBy())
                    ->where('id', '!=', $courtId)
                    ->exists();

                if ($exists) {
                    return redirect()->back()->with('error', 'Court with this name already exists.');
                }

                $court->update($validated);

                return redirect()->back()->with('success', 'Court updated successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to update court');
            }
        } else {
            return redirect()->back()->with('error', 'Court not found.');
        }
    }

    public function show($courtId)
    {
        $court = Court::with(['creator'])
            ->where('id', $courtId)
            ->where('created_by', createdBy())
            ->first();

        if (!$court) {
            return redirect()->route('courts.index')->with('error', 'Court not found.');
        }

        return Inertia::render('courts/show', [
            'court' => $court,
        ]);
    }

    public function destroy($courtId)
    {
        $court = Court::where('id', $courtId)
            ->where('created_by', createdBy())
            ->first();

        if ($court) {
            try {
                $court->delete();
                return redirect()->back()->with('success', 'Court deleted successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to delete court');
            }
        } else {
            return redirect()->back()->with('error', 'Court not found.');
        }
    }

    public function toggleStatus($courtId)
    {
        $court = Court::where('id', $courtId)
            ->where('created_by', createdBy())
            ->first();

        if ($court) {
            try {
                $court->status = $court->status === 'active' ? 'inactive' : 'active';
                $court->save();

                return redirect()->back()->with('success', 'Court status updated successfully');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: 'Failed to update court status');
            }
        } else {
            return redirect()->back()->with('error', 'Court not found.');
        }
    }
}
