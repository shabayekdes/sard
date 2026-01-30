<?php

namespace App\Http\Controllers;

use App\Models\CaseTimeline;
use App\Models\CaseModel;
use App\Models\Setting;
use App\Services\GoogleCalendarService;
use App\Services\EmailTemplateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CaseTimelineController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseTimeline::withPermissionCheck()
            ->with(['case', 'creator']);

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('event_type', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('case_id') && !empty($request->case_id) && $request->case_id !== 'all') {
            $query->where('case_id', $request->case_id);
        }

        if ($request->has('event_type') && !empty($request->event_type) && $request->event_type !== 'all') {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('event_date', 'asc');
        }

        $timelines = $query->paginate($request->per_page ?? 10);
        $cases = CaseModel::withPermissionCheck()->where('status', 'active')->get(['id', 'title', 'case_id']);

        $googleCalendarEnabled = Setting::where('user_id', createdBy())
            ->where('key', 'googleCalendarEnabled')
            ->value('value') == '1';

        return Inertia::render('cases/case-timelines/index', [
            'timelines' => $timelines,
            'cases' => $cases,
            'googleCalendarEnabled' => $googleCalendarEnabled,
            'filters' => $request->all(['search', 'case_id', 'event_type', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'event_type_id' => 'required|exists:event_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_completed' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
            'sync_with_google_calendar' => 'nullable|boolean',
        ]);

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['is_completed'] = $validated['is_completed'] ?? false;

        $case = CaseModel::withPermissionCheck()->with('client')->where('id', $validated['case_id'])->first();
        if (!$case) {
            return redirect()->back()->with('error', 'Invalid case selected.');
        }

        $timeline = CaseTimeline::create($validated);

        // Handle Google Calendar sync and meeting link creation
        $meetingLink = null;
        if ($timeline && $request->sync_with_google_calendar) {
            $calendarService = new GoogleCalendarService();
            $createMeetingLink = $request->create_meeting_link ?? false;
            $result = $calendarService->createEvent($timeline, createdBy(), 'timeline', $createMeetingLink);
            
            if ($result) {
                if (is_array($result)) {
                    // Meeting link was created
                    $timeline->update([
                        'google_calendar_event_id' => $result['event_id'],
                        'meeting_link' => $result['meeting_link']
                    ]);
                    $meetingLink = $result['meeting_link'];
                } else {
                    // Just event ID returned
                    $timeline->update(['google_calendar_event_id' => $result]);
                }
            }
        }

        // Send email notification to client if meeting link was created
        if ($meetingLink && $case->client && $case->client->email) {
            $this->sendMeetingLinkNotification($case, $timeline, $meetingLink);
        }

        return redirect()->back()->with('success', 'Timeline event created successfully.');
    }

    public function update(Request $request, $timelineId)
    {
        $timeline = CaseTimeline::withPermissionCheck()->where('id', $timelineId)->first();

        if (!$timeline) {
            return redirect()->back()->with('error', 'Timeline event not found.');
        }

        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id',
            'event_type_id' => 'required|exists:event_types,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_completed' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
            'sync_with_google_calendar' => 'nullable|boolean',
        ]);

        $validated['is_completed'] = $validated['is_completed'] ?? false;

        $case = CaseModel::withPermissionCheck()->where('id', $validated['case_id'])->first();
        if (!$case) {
            return redirect()->back()->with('error', 'Invalid case selected.');
        }

        $timeline->update($validated);

        // Handle Google Calendar sync
        $calendarService = new GoogleCalendarService();
        
        if ($timeline->google_calendar_event_id) {
            // If timeline already has Google Calendar event, update it automatically
            $calendarService->updateEvent($timeline->google_calendar_event_id, $timeline, createdBy(), 'timeline');
        } elseif ($request->sync_with_google_calendar) {
            // Create new Google Calendar event if sync is requested
            $eventId = $calendarService->createEvent($timeline, createdBy(), 'timeline');
            if ($eventId) {
                $timeline->update(['google_calendar_event_id' => $eventId]);
            }
        }

        return redirect()->back()->with('success', 'Timeline event updated successfully.');
    }

    public function destroy($timelineId)
    {
        $timeline = CaseTimeline::withPermissionCheck()->where('id', $timelineId)->first();

        if (!$timeline) {
            return redirect()->back()->with('error', 'Timeline event not found.');
        }

        // Delete Google Calendar event if exists
        if ($timeline->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $calendarService->deleteEvent($timeline->google_calendar_event_id, createdBy());
        }

        $timeline->delete();

        return redirect()->back()->with('success', 'Timeline event deleted successfully.');
    }

    public function toggleStatus($timelineId)
    {
        $timeline = CaseTimeline::withPermissionCheck()->where('id', $timelineId)->first();

        if (!$timeline) {
            return redirect()->back()->with('error', 'Timeline event not found.');
        }

        $timeline->status = $timeline->status === 'active' ? 'inactive' : 'active';
        $timeline->save();

        return redirect()->back()->with('success', 'Timeline event status updated successfully.');
    }

    /**
     * Send meeting link notification to client
     */
    private function sendMeetingLinkNotification($case, $timeline, $meetingLink)
    {
        try {
            if (isEmailTemplateEnabled('Timeline Meeting Link', createdBy())) {
                $emailService = new EmailTemplateService();
                $client = $case->client;

                if (!$client || !$client->email) {
                    return;
                }

                $eventType = $timeline->eventType;
                $eventTypeName = $eventType ? (is_string($eventType->name) ? $eventType->name : ($eventType->name['en'] ?? 'Event')) : 'Event';

                $variables = [
                    '{user_name}' => auth()->user()->name ?? 'System Administrator',
                    '{client}' => $client->name ?? 'Client',
                    '{case}' => $case->title ?? 'N/A',
                    '{case_id}' => $case->case_id ?? 'N/A',
                    '{event_title}' => $timeline->title ?? 'Event',
                    '{event_type}' => $eventTypeName,
                    '{event_date}' => $timeline->event_date ? $timeline->event_date->format('F j, Y \a\t g:i A') : 'Not specified',
                    '{meeting_link}' => $meetingLink,
                    '{description}' => $timeline->description ?? '',
                    '{app_name}' => config('app.name', 'Legal Management System'),
                ];

                $userLanguage = auth()->user()->lang ?? 'en';

                $emailService->sendTemplateEmailWithLanguage(
                    'Timeline Meeting Link',
                    $variables,
                    (string) $client->email,
                    (string) $client->name,
                    $userLanguage
                );
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send meeting link notification: ' . $e->getMessage());
        }
    }
}