<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\CaseModel;
use App\Models\CaseTeamMember;
use App\Models\Hearing;
use App\Models\Court;
use App\Models\HearingType;
use App\Models\MediaItem;
use App\Models\User;
use App\Models\Setting;
use App\Models\CaseTimeline;
use App\Models\EventType;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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

        if ($request->filled('hearing_type_id') && $request->hearing_type_id !== 'all') {
            $query->where('hearing_type_id', $request->hearing_type_id);
        }

        if ($request->filled('case_id') && $request->case_id !== 'all') {
            $query->where('case_id', $request->case_id);
        }

        if ($request->filled('assigned_to') && $request->assigned_to !== 'all') {
            $query->whereHas('teamMembers', function ($q) use ($request) {
                $q->where('users.id', $request->assigned_to);
            });
        }

        if ($request->filled('hearing_date_from')) {
            $query->whereDate('hearing_date', '>=', $request->hearing_date_from);
        }

        if ($request->filled('hearing_date_to')) {
            $query->whereDate('hearing_date', '<=', $request->hearing_date_to);
        }

        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('hearing_date', 'desc');
        }

        $hearings = $query->paginate($request->per_page ?? 10);

        $cases = CaseModel::withPermissionCheck()->get(['id', 'case_id', 'title', 'file_number']);
        $hearingTypes = HearingType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);
        $hearingFilterUsers = User::withPermissionCheck()
            ->where('status', 'active')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

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
            'hearingTypes' => $hearingTypes,
            'hearingFilterUsers' => $hearingFilterUsers,
            'googleCalendarEnabled' => $googleCalendarEnabled,
            'hearingStats' => $hearingStats,
            'filters' => $request->all([
                'search',
                'status',
                'hearing_type_id',
                'case_id',
                'assigned_to',
                'hearing_date_from',
                'hearing_date_to',
                'sort_field',
                'sort_direction',
                'per_page',
            ]),
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('hearings/form', $this->hearingFormPageProps($request, null));
    }

    public function show(Request $request, $id)
    {
        $hearing = Hearing::withPermissionCheck()
            ->with([
                'case.client:id,name',
                'court.courtType',
                'court.circleType',
                'hearingType',
                'teamMembers' => function ($query) {
                    $query->with('roles');
                },
            ])
            ->where('id', $id)
            ->first();

        if (!$hearing) {
            return redirect()->route('hearings.index')->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        $reminderMinutes = \App\Models\HearingNotification::where('hearing_id', $hearing->id)
            ->where('status', 'pending')
            ->orderBy('minutes_before')
            ->pluck('minutes_before')
            ->map(fn ($m) => (int) $m)
            ->values()
            ->all();

        $returnToCaseId = null;
        if ($request->filled('case_id')) {
            $returnToCaseId = (int) $request->query('case_id');
        }

        return Inertia::render('hearings/show', [
            'hearing' => $hearing,
            'hearingAttachmentMedia' => $this->hearingAttachmentMediaList($hearing),
            'reminderMinutes' => $reminderMinutes,
            'returnToCaseId' => $returnToCaseId,
        ]);
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

        $prefillCourtId = null;
        if (!$hearing && $queryCaseId) {
            $prefillCase = CaseModel::withPermissionCheck()
                ->where('id', $queryCaseId)
                ->first(['id', 'court_id']);
            if ($prefillCase && $prefillCase->court_id) {
                $courtOk = Court::withPermissionCheck()
                    ->where('id', $prefillCase->court_id)
                    ->where('status', 'active')
                    ->exists();
                if ($courtOk) {
                    $prefillCourtId = (int) $prefillCase->court_id;
                }
            }
        }

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
            'prefillCourtId' => $prefillCourtId,
            'returnToCaseId' => $returnToCaseId,
            'reminderMinutes' => $reminderMinutes,
            'teamMemberOptions' => $this->hearingCaseTeamUserOptions($initialCaseId),
            'hearingAttachmentMedia' => $hearing
                ? $this->hearingAttachmentMediaList($hearing)
                : [],
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

        $teamMemberIds = $this->normalizeTeamMemberIds($request, (int) $validated['case_id']);
        unset($validated['team_member_ids'], $validated['attachments']);
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
        $attachmentInput = $this->parseHearingAttachmentIdsFromRequest($request, null);
        if ($attachmentInput === null) {
            $attachmentInput = [];
        }
        $this->syncHearingAttachmentMedia($hearing, $attachmentInput);

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

        $teamMemberIds = $this->normalizeTeamMemberIds($request, (int) $validated['case_id']);
        unset($validated['team_member_ids'], $validated['attachments']);
        $hearing->update($validated);
        $this->syncHearingTeamMembers($hearing, $teamMemberIds);
        $attachmentInput = $this->parseHearingAttachmentIdsFromRequest($request, $hearing);
        if ($attachmentInput !== null) {
            $this->syncHearingAttachmentMedia($hearing, $attachmentInput);
        }

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

    public function updateMinutes(Request $request, int $id)
    {
        $hearing = Hearing::withPermissionCheck()
            ->where('id', $id)
            ->first();

        if (!$hearing) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        $validated = $request->validate([
            'minutes_title' => 'nullable|string|max:500',
            'minutes_date' => 'nullable|date',
            'minutes_content' => 'nullable|string',
        ]);

        $hearing->update($validated);

        return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Hearing')]));
    }

    public function updateAttachments(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $hearing = Hearing::withPermissionCheck()
            ->where('id', $id)
            ->first();

        if (!$hearing) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        $request->validate([
            'attachments' => 'nullable|array',
            'attachments.*' => 'integer|min:1',
        ]);

        $attachmentInput = $this->parseHearingAttachmentIdsFromRequest($request, $hearing);
        if ($attachmentInput === null) {
            $attachmentInput = [];
        }
        $this->syncHearingAttachmentMedia($hearing, $attachmentInput);

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

    public function attachTeamMember(Request $request, int $hearing): \Illuminate\Http\RedirectResponse
    {
        $model = Hearing::withPermissionCheck()
            ->where('id', $hearing)
            ->first();

        if (!$model) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        if (!$model->case_id) {
            return redirect()->back()->with('error', __('Hearing has no case.'));
        }

        $validated = $request->validate([
            'user_id' => 'required|integer',
        ]);

        $userId = (int) $validated['user_id'];

        $request->merge(['team_member_ids' => [$userId]]);

        $allowed = $this->normalizeTeamMemberIds($request, (int) $model->case_id);
        if (!in_array($userId, $allowed, true)) {
            return redirect()->back()->with('error', __('This user is not on the case team.'));
        }

        if ($model->teamMembers()->where('users.id', $userId)->exists()) {
            return redirect()->back()->with('error', __('User is already assigned to this session.'));
        }

        $tenantId = (string) $model->tenant_id;
        $model->teamMembers()->attach($userId, ['tenant_id' => $tenantId]);

        return redirect()->back()->with('success', __('Team member added to session.'));
    }

    public function detachTeamMember(Request $request, int $hearing, int $user): \Illuminate\Http\RedirectResponse
    {
        $model = Hearing::withPermissionCheck()
            ->where('id', $hearing)
            ->first();

        if (!$model) {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Hearing')]));
        }

        if (!$model->teamMembers()->where('users.id', $user)->exists()) {
            return redirect()->back()->with('error', __('User is not assigned to this session.'));
        }

        $model->teamMembers()->detach($user);

        return redirect()->back()->with('success', __('Team member removed from session.'));
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
     * Media rows for the hearing (Spatie, ordered).
     *
     * @return list<array{id: int, name: string, file_name: string, url: string, thumb_url: string, size: int, mime_type: string|null}>
     */
    private function hearingAttachmentMediaList(Hearing $hearing): array
    {
        $out = [];
        foreach ($hearing->getMedia(Hearing::HEARING_FILES_COLLECTION)->sortBy('order_column') as $m) {
            try {
                $originalUrl = $this->hearingMediaFullUrl($m->getUrl());
                $thumbUrl = $originalUrl;
                try {
                    $thumbUrl = $this->hearingMediaFullUrl($m->getUrl('thumb'));
                } catch (\Exception $e) {
                }
                $out[] = [
                    'id' => (int) $m->id,
                    'name' => $m->name,
                    'file_name' => $m->file_name,
                    'url' => $originalUrl,
                    'thumb_url' => $thumbUrl,
                    'size' => (int) $m->size,
                    'mime_type' => $m->mime_type,
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        return $out;
    }

    private function hearingMediaFullUrl(string $url): string
    {
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        $baseUrl = request()->getSchemeAndHttpHost();

        return $baseUrl . $url;
    }

    /**
     * Ordered library media ids and/or this hearing’s media row ids, filtered by permission.
     * `null` = the request had no "attachments" key (skip sync; edit form partial submit).
     *
     * @return list<int>|null
     */
    private function parseHearingAttachmentIdsFromRequest(Request $request, ?Hearing $hearing): ?array
    {
        if (! array_key_exists('attachments', $request->all())) {
            return $hearing ? null : [];
        }
        $raw = $request->input('attachments');
        if ($raw === null || $raw === '' || (is_array($raw) && $raw === [])) {
            return [];
        }
        if (is_string($raw)) {
            $ids = array_map('intval', array_filter(array_map('trim', explode(',', $raw))));
        } elseif (is_array($raw)) {
            $ids = array_map('intval', array_values(array_filter($raw)));
        } else {
            return [];
        }
        $ids = array_values(array_filter($ids, fn (int $id) => $id > 0));
        if ($ids === []) {
            return [];
        }

        return $this->filterAllowlistedAttachmentSourceIds($hearing, $ids);
    }

    /**
     * @param  list<int>  $orderedRequestIds
     * @return list<int>
     */
    private function filterAllowlistedAttachmentSourceIds(?Hearing $hearing, array $orderedRequestIds): array
    {
        $user = auth()->user();
        $tenantId = createdBy();
        $hearingId = $hearing?->id;
        $onHearingSet = $hearing
            ? array_fill_keys(
                $hearing->getMedia(Hearing::HEARING_FILES_COLLECTION)->pluck('id')->map(fn ($k) => (int) $k)->all(),
                true,
            )
            : [];

        $out = [];
        $seen = [];
        foreach ($orderedRequestIds as $rawId) {
            $id = (int) $rawId;
            if ($id < 1) {
                continue;
            }
            if (isset($seen[$id])) {
                continue;
            }
            $m = Media::query()->whereKey($id)->first();
            if (! $m) {
                continue;
            }
            if ($m->model_type === Hearing::class
                && (int) $m->model_id === (int) $hearingId
                && $m->collection_name === Hearing::HEARING_FILES_COLLECTION) {
                if (isset($onHearingSet[$m->id])) {
                    $out[] = (int) $m->id;
                    $seen[$id] = true;
                }
                continue;
            }
            if ($m->model_type === MediaItem::class && $this->mediaFromLibraryPassesTenant($m, $user, $tenantId)) {
                $out[] = (int) $m->id;
                $seen[$id] = true;
            }
        }

        return $out;
    }

    private function mediaFromLibraryPassesTenant(Media $m, $user, $tenantId): bool
    {
        if ($m->model_type !== MediaItem::class) {
            return false;
        }
        if ($user && ($user->type === 'superadmin' || $user->hasPermissionTo('manage-any-media'))) {
            return true;
        }
        if ($m->tenant_id === null) {
            return true;
        }

        return (string) $m->tenant_id === (string) $tenantId;
    }

    /**
     * @param  list<int>  $orderedSourceIds
     */
    private function syncHearingAttachmentMedia(Hearing $hearing, array $orderedSourceIds): void
    {
        if ($orderedSourceIds === []) {
            $hearing->clearMediaCollection(Hearing::HEARING_FILES_COLLECTION);

            return;
        }

        $hearing = $hearing->fresh() ?? $hearing;
        $resolvedHearingFileIds = [];
        $seen = [];
        foreach ($orderedSourceIds as $sourceId) {
            $m = Media::query()->whereKey($sourceId)->first();
            if (! $m) {
                continue;
            }
            if ($m->model_type === Hearing::class && (int) $m->model_id === (int) $hearing->id
                && $m->collection_name === Hearing::HEARING_FILES_COLLECTION) {
                $hid = (int) $m->id;
            } elseif ($m->model_type === MediaItem::class) {
                $existing = $hearing->getMedia(Hearing::HEARING_FILES_COLLECTION)
                    ->first(function ($h) use ($m) {
                        return (int) $h->getCustomProperty('imported_from_media_id', 0) === (int) $m->id;
                    });
                if ($existing) {
                    $hid = (int) $existing->id;
                } else {
                    $created = $this->copyMediaRowToHearing($hearing, $m);
                    if (! $created) {
                        continue;
                    }
                    $hid = (int) $created->id;
                }
            } else {
                continue;
            }
            if (isset($seen[$hid])) {
                continue;
            }
            $resolvedHearingFileIds[] = $hid;
            $seen[$hid] = true;
        }

        $keep = array_fill_keys($resolvedHearingFileIds, true);
        foreach ($hearing->getMedia(Hearing::HEARING_FILES_COLLECTION) as $existing) {
            if (empty($keep[$existing->id] ?? null)) {
                $existing->delete();
            }
        }
        $hearing->load('media');
        foreach (array_values($resolvedHearingFileIds) as $i => $mediaId) {
            Media::query()->whereKey($mediaId)->update(['order_column' => $i + 1]);
        }
    }

    private function copyMediaRowToHearing(Hearing $hearing, Media $source): ?Media
    {
        if ($source->model_type !== MediaItem::class) {
            return null;
        }
        $filePath = $this->getAbsoluteFilePathForSpatieMedia($source);
        if (! $filePath) {
            return null;
        }
        $isTemp = str_starts_with($filePath, sys_get_temp_dir());
        try {
            $new = $hearing->addMedia($filePath)
                ->usingName($source->name)
                ->usingFileName($source->file_name)
                ->withCustomProperties(array_merge(
                    is_array($source->custom_properties) ? $source->custom_properties : (array) $source->custom_properties,
                    ['imported_from_media_id' => (int) $source->id]
                ))
                ->toMediaCollection(Hearing::HEARING_FILES_COLLECTION, $source->disk);
            $new->tenant_id = (string) $hearing->tenant_id;
            $new->save();

            return $new;
        } catch (\Throwable) {
            return null;
        } finally {
            if ($isTemp && is_file($filePath)) {
                @unlink($filePath);
            }
        }
    }

    private function getAbsoluteFilePathForSpatieMedia(Media $media): ?string
    {
        try {
            $path = $media->getPath();
            if (is_string($path) && is_readable($path)) {
                return $path;
            }
        } catch (\Throwable) {
        }
        $pathGenerator = app(config('media-library.path_generator'));
        $relativePath = $pathGenerator->getPath($media) . $media->file_name;
        $disk = $media->disk ?? (string) config('media-library.disk_name', 'public');
        $storage = Storage::disk($disk);
        if (! $storage->exists($relativePath)) {
            return null;
        }
        if (in_array($disk, ['public', 'local'], true) && method_exists($storage, 'path')) {
            $full = $storage->path($relativePath);
            if (is_readable($full)) {
                return $full;
            }
        }
        $tmp = @tempnam(sys_get_temp_dir(), 'hm_');
        if (! $tmp) {
            return null;
        }
        $contents = $storage->get($relativePath);
        if ($contents === null || $contents === '') {
            @unlink($tmp);

            return null;
        }
        if (@file_put_contents($tmp, $contents) === false) {
            @unlink($tmp);

            return null;
        }

        return $tmp;
    }
}
