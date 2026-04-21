<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\CaseModel;
use App\Models\CaseTeamMember;
use App\Models\Hearing;
use App\Models\Court;
use App\Models\HearingType;
use App\Models\MediaItem;
use App\Models\Setting;
use App\Models\CaseTimeline;
use App\Models\EventType;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HearingController extends BaseController
{
    public function index(Request $request)
    {
        $query = Hearing::withPermissionCheck()
            ->with([
                'case',
                'court.courtType',
                'court.circleType',
                'hearingType',
                'teamMembers',
            ]);

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('hearing_id', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhereHas('case', function($caseQuery) use ($request) {
                        $caseQuery->where('case_id', 'like', '%' . $request->search . '%')
                            ->orWhere('title', 'like', '%' . $request->search . '%')
                            ->orWhere('file_number', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('court', function($courtQuery) use ($request) {
                        // Search in court name or circle number if it exists
                        $courtQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('court_id') && !empty($request->court_id) && $request->court_id !== 'all') {
            $query->where('court_id', $request->court_id);
        }

        if ($request->has('court_type_id') && !empty($request->court_type_id) && $request->court_type_id !== 'all') {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('court_type_id', $request->court_type_id);
            });
        }

        if ($request->has('circle_type_id') && !empty($request->circle_type_id) && $request->circle_type_id !== 'all') {
            $query->whereHas('court', function($q) use ($request) {
                $q->where('circle_type_id', $request->circle_type_id);
            });
        }

        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('hearing_date', 'desc');
        }

        $hearings = $query->paginate($request->per_page ?? 10);

        $cases = CaseModel::withPermissionCheck()->get(['id', 'case_id', 'title', 'file_number']);
        $courts = Court::withPermissionCheck()
            ->with(['courtType', 'circleType'])
            ->where('status', 'active')
            ->get(['id', 'name', 'court_type_id', 'circle_type_id']);
        $courtTypes = \App\Models\CourtType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);
        $circleTypes = \App\Models\CircleType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);
        $hearingTypes = HearingType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);

        $googleCalendarEnabled = Settings::boolean('GOOGLE_CALENDAR_ENABLED');

        $nowRiyadh = Carbon::now('Asia/Riyadh');
        $dayOfWeek = $nowRiyadh->dayOfWeek; // 0 = Sunday .. 6 = Saturday
        $weekStart = $nowRiyadh->copy()->subDays($dayOfWeek)->startOfDay();
        $weekEnd = $weekStart->copy()->addDays(6);

        $hearingStats = [
            'total' => Hearing::withPermissionCheck()->count(),
            'this_week' => Hearing::withPermissionCheck()
                ->whereBetween('hearing_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->count(),
            'scheduled' => Hearing::withPermissionCheck()
                ->where('status', 'scheduled')
                ->count(),
            'completed' => Hearing::withPermissionCheck()
                ->where('status', 'completed')
                ->count(),
        ];

        return Inertia::render('hearings/index', [
            'hearings' => $hearings,
            'cases' => $cases,
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'circleTypes' => $circleTypes,
            'hearingTypes' => $hearingTypes,
            'googleCalendarEnabled' => $googleCalendarEnabled,
            'hearingStats' => $hearingStats,
            'filters' => $request->all(['search', 'status', 'court_id', 'court_type_id', 'circle_type_id', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('hearings/form', $this->hearingFormPageProps($request, null));
    }

    public function edit(Request $request, $id)
    {
        $hearing = Hearing::withPermissionCheck()
            ->with(['case', 'court.courtType', 'court.circleType', 'hearingType', 'teamMembers:id,name,email'])
            ->where('id', $id)
            ->first();

        if (!$hearing) {
            return redirect()->route('hearings.index')->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        return Inertia::render('hearings/form', $this->hearingFormPageProps($request, $hearing));
    }

    /**
     * @return array<string, mixed>
     */
    private function hearingFormPageProps(Request $request, ?Hearing $hearing = null): array
    {
        $cases = CaseModel::withPermissionCheck()->get(['id', 'case_id', 'title', 'file_number']);
        $courts = Court::withPermissionCheck()
            ->with(['courtType', 'circleType'])
            ->where('status', 'active')
            ->get(['id', 'name', 'court_type_id', 'circle_type_id']);
        $courtTypes = \App\Models\CourtType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);
        $circleTypes = \App\Models\CircleType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);
        $hearingTypes = HearingType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);
        $googleCalendarEnabled = Settings::boolean('GOOGLE_CALENDAR_ENABLED');

        $fromCase = $request->boolean('from_case');
        $queryCaseId = $request->query('case_id') ? (int) $request->query('case_id') : null;
        $returnToCaseId = null;
        if ($fromCase) {
            $returnToCaseId = $hearing?->case_id ?? $queryCaseId;
        }

        $hideCaseField = $fromCase && ($hearing?->case_id || $queryCaseId);
        $reminderMinutes = [];
        if ($hearing) {
            $reminderMinutes = \App\Models\HearingNotification::where('hearing_id', $hearing->id)
                ->where('status', 'pending')
                ->orderBy('minutes_before')
                ->pluck('minutes_before')
                ->map(fn ($m) => (int) $m)
                ->values()
                ->all();
        }

        $initialCaseId = $hearing?->case_id ?? ($queryCaseId ? (int) $queryCaseId : null);

        if ($hearing) {
            $hearing->loadMissing('teamMembers:id,name,email');
        }

        return [
            'mode' => $hearing ? 'edit' : 'create',
            'hearing' => $hearing,
            'cases' => $cases,
            'courts' => $courts,
            'courtTypes' => $courtTypes,
            'circleTypes' => $circleTypes,
            'hearingTypes' => $hearingTypes,
            'googleCalendarEnabled' => $googleCalendarEnabled,
            'prefillCaseId' => $queryCaseId,
            'hideCaseField' => $hideCaseField,
            'returnToCaseId' => $returnToCaseId,
            'reminderMinutes' => $reminderMinutes,
            'teamMemberOptions' => $this->hearingCaseTeamUserOptions($initialCaseId),
        ];
    }

    public function caseTeamUsers(int $caseId)
    {
        return response()->json(['users' => $this->hearingCaseTeamUserOptions($caseId)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id,tenant_id,' . createdBy(),
            'court_id' => 'nullable|exists:courts,id,tenant_id,' . createdBy(),
            'circle_number' => 'nullable|string|max:255',
            'judge_name' => 'nullable|string|max:255',
            'hearing_type_id' => 'required|exists:hearing_types,id,tenant_id,' . createdBy(),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hearing_date' => 'required|date|after_or_equal:today',
            'hearing_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'reminder_minutes' => 'nullable|array',
            'reminder_minutes.*' => 'integer|min:1|max:10080',
            'url' => 'nullable|url|max:500',
            'status' => 'nullable|in:scheduled,in_progress,completed,postponed,cancelled',
            'notes' => 'nullable|string',
            'attachments' => 'nullable',
            'team_member_ids' => 'nullable|array',
            'team_member_ids.*' => 'integer',
            'sync_with_google_calendar' => 'nullable|boolean',
        ]);

        $validated['attachments'] = $this->normalizeHearingAttachments($request);
        $teamMemberIds = $this->normalizeTeamMemberIds($request, (int) $validated['case_id']);
        unset($validated['team_member_ids']);
        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'scheduled';
        $validated['duration_minutes'] = $validated['duration_minutes'] ?? 30;
        $validatedReminderMinutes = collect($validated['reminder_minutes'] ?? [])
            ->map(fn ($m) => (int) $m)
            ->filter(fn ($m) => $m > 0)
            ->unique()
            ->values()
            ->all();

        $hearing = Hearing::create($validated);
        $this->syncHearingTeamMembers($hearing, $teamMemberIds);

        // Handle Google Calendar sync
        if ($hearing && $request->sync_with_google_calendar) {
            $calendarService = new GoogleCalendarService();
            $eventId = $calendarService->createEvent($hearing, createdBy(), 'hearing');
            
            if ($eventId) {
                $hearing->update(['google_calendar_event_id' => $eventId]);
            }
        }

        // Load relationships for email
        $hearing->load(['hearingType', 'court', 'case.client']);

        // Create default notifications
        $this->createDefaultNotifications($hearing, $validatedReminderMinutes);

        // Trigger notifications
        event(new \App\Events\NewHearingCreated($hearing, $request->all()));

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
            $message = __('Hearing scheduled successfully, but ') . implode(', ', $errors);
            return redirect()->back()->with('warning', $message);
        }

        return redirect()->back()->with('success', __(':model scheduled successfully.', ['model' => __('Hearing')]));
    }

    public function update(Request $request, $id)
    {
        $hearing = Hearing::withPermissionCheck()
            ->where('id', $id)
            ->first();

        if (!$hearing) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        $validated = $request->validate([
            'case_id' => 'required|exists:cases,id,tenant_id,' . createdBy(),
            'court_id' => 'nullable|exists:courts,id,tenant_id,' . createdBy(),
            'circle_number' => 'nullable|string|max:255',
            'judge_name' => 'nullable|string|max:255',
            'hearing_type_id' => 'required|exists:hearing_types,id,tenant_id,' . createdBy(),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hearing_date' => 'required|date|after_or_equal:today',
            'hearing_time' => 'required|date_format:H:i',
            'duration_minutes' => 'nullable|integer|min:15|max:480',
            'reminder_minutes' => 'nullable|array',
            'reminder_minutes.*' => 'integer|min:1|max:10080',
            'url' => 'nullable|url|max:500',
            'status' => 'nullable|in:scheduled,in_progress,completed,postponed,cancelled',
            'notes' => 'nullable|string',
            'attachments' => 'nullable',
            'team_member_ids' => 'nullable|array',
            'team_member_ids.*' => 'integer',
            'outcome' => 'nullable|string',
            'sync_with_google_calendar' => 'nullable|boolean',
        ]);

        $validated['attachments'] = $this->normalizeHearingAttachments($request);
        $teamMemberIds = $this->normalizeTeamMemberIds($request, (int) $validated['case_id']);
        unset($validated['team_member_ids']);
        $hearing->update($validated);
        $this->syncHearingTeamMembers($hearing, $teamMemberIds);

        // Handle Google Calendar sync
        if ($request->sync_with_google_calendar && !$hearing->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $eventId = $calendarService->createEvent($hearing, createdBy(), 'hearing');
            if ($eventId) {
                $hearing->update(['google_calendar_event_id' => $eventId]);
            }
        } elseif ($request->sync_with_google_calendar && $hearing->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $calendarService->updateEvent($hearing->google_calendar_event_id, $hearing, createdBy(), 'hearing');
        } elseif (!$request->sync_with_google_calendar && $hearing->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $calendarService->deleteEvent($hearing->google_calendar_event_id, createdBy());
            $hearing->update(['google_calendar_event_id' => null]);
        }

        // Update notifications if date/time changed
        $validatedReminderMinutes = collect($validated['reminder_minutes'] ?? [])
            ->map(fn ($m) => (int) $m)
            ->filter(fn ($m) => $m > 0)
            ->unique()
            ->values()
            ->all();

        if (isset($validated['hearing_date']) || isset($validated['hearing_time']) || isset($validated['reminder_minutes'])) {
            $this->updateNotifications($hearing, $validatedReminderMinutes);
        }

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Hearing')]));
    }

    public function destroy($id)
    {
        $hearing = Hearing::withPermissionCheck()
            ->where('id', $id)
            ->first();

        if (!$hearing) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        // Delete Google Calendar event if exists
        if ($hearing->google_calendar_event_id) {
            $calendarService = new GoogleCalendarService();
            $calendarService->deleteEvent($hearing->google_calendar_event_id, createdBy());
        }

        $hearing->delete();

        return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Hearing')]));
    }

    private function createDefaultNotifications($hearing, array $customReminderMinutes = [])
    {
        $reminderTimes = $customReminderMinutes;
        if (empty($reminderTimes)) {
            return;
        }
        $date = date('Y-m-d', strtotime($hearing->hearing_date));
        $time = date('H:i', strtotime($hearing->hearing_time));
        $hearingDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

        foreach ($reminderTimes as $minutes) {
            \App\Models\HearingNotification::create([
                'hearing_id' => $hearing->id,
                'tenant_id' => createdBy(),
                'type' => 'system',
                'minutes_before' => $minutes,
                'scheduled_at' => $hearingDateTime->copy()->subMinutes($minutes),
                'status' => 'pending'
            ]);
        }
    }

    private function updateNotifications($hearing, array $customReminderMinutes = [])
    {
        // Delete existing pending notifications
        \App\Models\HearingNotification::where('hearing_id', $hearing->id)
            ->where('status', 'pending')
            ->delete();

        // Create new notifications
        $this->createDefaultNotifications($hearing, $customReminderMinutes);
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    private function hearingCaseTeamUserOptions(?int $caseId): array
    {
        if (!$caseId) {
            return [];
        }

        $case = CaseModel::withPermissionCheck()
            ->where('id', $caseId)
            ->with(['teamMembers' => function ($q) {
                $q->where('status', 'active');
            }, 'teamMembers.user'])
            ->first();

        if (!$case) {
            return [];
        }

        return $case->teamMembers
            ->filter(fn ($tm) => $tm->user)
            ->map(function ($tm) {
                $user = $tm->user;
                $label = $user->name;
                if (!empty($user->email)) {
                    $label .= ' (' . $user->email . ')';
                }

                return [
                    'value' => (int) $user->id,
                    'label' => $label,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<int>
     */
    private function normalizeTeamMemberIds(Request $request, int $caseId): array
    {
        $raw = $request->input('team_member_ids', []);
        if (!is_array($raw) || $raw === []) {
            return [];
        }

        $ids = collect($raw)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $allowed = CaseTeamMember::query()
            ->where('case_id', $caseId)
            ->where('tenant_id', createdBy())
            ->where('status', 'active')
            ->pluck('user_id');

        return $ids->intersect($allowed)->values()->all();
    }

    /**
     * @param  list<int>  $userIds
     */
    private function syncHearingTeamMembers(Hearing $hearing, array $userIds): void
    {
        $tenantId = (string) $hearing->tenant_id;
        $payload = [];
        foreach (array_values(array_unique(array_map('intval', $userIds))) as $id) {
            if ($id > 0) {
                $payload[$id] = ['tenant_id' => $tenantId];
            }
        }
        $hearing->teamMembers()->sync($payload);
    }

    /**
     * Store only Spatie media row ids (files that exist in the tenant media library).
     *
     * @return array<int, int>|null
     */
    private function normalizeHearingAttachments(Request $request): ?array
    {
        if (!$request->has('attachments') || $request->attachments === null || $request->attachments === '') {
            return null;
        }

        $raw = $request->input('attachments');
        if (is_string($raw)) {
            $ids = array_map('intval', array_filter(array_map('trim', explode(',', $raw))));
        } elseif (is_array($raw)) {
            $ids = array_map('intval', array_values(array_filter($raw)));
        } else {
            return null;
        }

        $ids = array_values(array_unique(array_filter($ids, fn (int $id) => $id > 0)));

        if ($ids === []) {
            return null;
        }

        $tenantId = createdBy();
        $user = auth()->user();

        $query = Media::query()
            ->whereIn('id', $ids)
            ->where('model_type', MediaItem::class);

        if ($user && ($user->type === 'superadmin' || $user->hasPermissionTo('manage-any-media'))) {
            // no extra tenant filter
        } else {
            $query->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
            });
        }

        $allowed = $query->pluck('id')->all();
        $allowedSet = array_fill_keys($allowed, true);

        $ordered = [];
        foreach ($ids as $id) {
            if (!empty($allowedSet[$id])) {
                $ordered[] = $id;
            }
        }

        return $ordered === [] ? null : $ordered;
    }
}
