<?php

namespace App\Services;

use App\Enums\SettingKey;
use App\Enums\TaskPriority;
use App\Facades\Settings;
use App\Models\Setting;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

class GoogleCalendarService
{
    private $client;

    private $service;

    public function __construct()
    {
        $this->client = new Google_Client;
        $this->service = new Google_Service_Calendar($this->client);
    }

    public function isEnabled($userId)
    {
        // Check if Google Calendar is globally enabled
        $globalEnabled = Settings::boolean(SettingKey::GoogleCalendarEnabled->value);
        \Log::info('Google Calendar enabled check', [
            'user_id' => $userId,
            'enabled' => $globalEnabled,
        ]);

        return $globalEnabled;
    }

    /**
     * Configure Google API client using tenant OAuth tokens (Integrations flow).
     */
    private function setupClient($tenantId): void
    {
        $this->client = new Google_Client;
        $this->service = new Google_Service_Calendar($this->client);
        $this->refreshGoogleClientWithStoredTokens($this->client, (string) $tenantId);
    }

    /**
     * Build a Google client with a fresh access token from the stored refresh token (for API calls outside this service instance).
     */
    public function createRefreshedOAuthClient(string $tenantId): Google_Client
    {
        $client = new Google_Client;
        $this->refreshGoogleClientWithStoredTokens($client, $tenantId);

        return $client;
    }

    /**
     * Apply OAuth app credentials, exchange refresh token, persist rotated tokens, set access token on $client.
     */
    private function refreshGoogleClientWithStoredTokens(Google_Client $client, string $tenantId): void
    {
        $this->configureOAuthClient($client, $tenantId);

        $refreshToken = Setting::query()
            ->where('tenant_id', $tenantId)
            ->where('key', 'GOOGLE_REFRESH_TOKEN')
            ->value('value');

        if ($refreshToken === null || $refreshToken === '') {
            throw new \Exception('Google Calendar is not connected. Connect from Electronic Integrations.');
        }

        $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);
        if (isset($token['error'])) {
            throw new \Exception((string) ($token['error_description'] ?? $token['error'] ?? 'Failed to refresh Google access token.'));
        }

        $this->persistOAuthTokensFromRefreshResponse($tenantId, $token, (string) $refreshToken);
        $client->setAccessToken($token);
    }

    /**
     * Apply tenant (or config) OAuth client ID/secret/redirect for Google Calendar API.
     */
    private function configureOAuthClient(Google_Client $client, string $tenantId): void
    {
        $settings = Setting::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('key', [
                SettingKey::GoogleCalendarClientId->value,
                SettingKey::GoogleCalendarClientSecret->value,
                SettingKey::GoogleCalendarRedirectUri->value,
                'googleCalendarClientId',
                'googleCalendarSecret',
                'googleCalendarRedirectUri',
            ])
            ->pluck('value', 'key');

        $clientId = $settings[SettingKey::GoogleCalendarClientId->value]
            ?? $settings['googleCalendarClientId']
            ?? config('services.google.client_id');
        $clientSecret = $settings[SettingKey::GoogleCalendarClientSecret->value]
            ?? $settings['googleCalendarSecret']
            ?? config('services.google.client_secret');
        $redirectUri = $settings[SettingKey::GoogleCalendarRedirectUri->value]
            ?? $settings['googleCalendarRedirectUri']
            ?? config('services.google.redirect')
            ?? route('google-calendar.callback');

        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception('Google OAuth client ID and secret are not configured.');
        }

        $client->setClientId((string) $clientId);
        $client->setClientSecret((string) $clientSecret);
        $client->setRedirectUri((string) $redirectUri);
        $client->setAccessType('offline');
        $client->addScope(Google_Service_Calendar::CALENDAR);
    }

    /**
     * Persist access/refresh token and expiry after a refresh_token exchange (shared with HTTP layer).
     *
     * @param  array<string, mixed>  $token
     */
    public function persistOAuthTokensFromRefreshResponse(string $tenantId, array $token, string $fallbackRefreshToken): void
    {
        $access = (string) ($token['access_token'] ?? '');
        if ($access === '') {
            return;
        }

        $expiresIn = (int) ($token['expires_in'] ?? 3600);
        $newRefresh = $token['refresh_token'] ?? null;
        $refreshToStore = is_string($newRefresh) && $newRefresh !== '' ? $newRefresh : $fallbackRefreshToken;

        Setting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => 'GOOGLE_TOKEN'],
            ['value' => $access, 'group' => 'integrations'],
        );
        Setting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => 'GOOGLE_REFRESH_TOKEN'],
            ['value' => $refreshToStore, 'group' => 'integrations'],
        );
        Setting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => 'GOOGLE_TOKEN_EXPIRES_AT'],
            ['value' => now()->addSeconds($expiresIn)->toDateTimeString(), 'group' => 'integrations'],
        );
    }

    private function googleCalendarEventSummary($item, string $type): string
    {
        if ($type === 'hearing') {
            $item->loadMissing(['case', 'court']);
            $caseTitle = $item->case?->title ?? '';
            $courtName = $item->court?->name ?? '';

            return $courtName !== ''
                ? __('Google Calendar hearing title with court', ['case_title' => $caseTitle, 'court_name' => $courtName])
                : __('Google Calendar hearing title case only', ['case_title' => $caseTitle]);
        }

        if ($type === 'timeline') {
            return (string) ($item->title ?? 'Event');
        }

        return '';
    }

    private function googleCalendarEventDescription($item, string $type): string
    {
        if ($type === 'hearing') {
            $parts = array_filter(
                [$item->description ?? '', $item->notes ?? ''],
                static fn (string $p) => $p !== ''
            );

            return implode("\n\n", $parts);
        }

        if ($type === 'timeline') {
            return (string) ($item->description ?? '');
        }

        return '';
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon}|null
     */
    private function resolveGoogleEventStartEnd($item, string $type): ?array
    {
        if ($type === 'hearing' && $item->hearing_date) {
            $startTime = \Carbon\Carbon::parse($item->hearing_date);
            if ($item->hearing_time) {
                $timeString = $item->hearing_time;
                if (strpos($timeString, ' ') !== false) {
                    $timeString = explode(' ', $timeString)[1];
                }
                $timeParts = explode(':', $timeString);
                $startTime->setTime((int) $timeParts[0], (int) $timeParts[1], 0);
            }
            $endTime = clone $startTime;
            $endTime->addMinutes((int) ($item->duration_minutes ?? 60));

            return [$startTime, $endTime];
        }

        if ($type === 'timeline' && $item->event_date) {
            $startTime = $item->event_date instanceof \Carbon\Carbon
                ? $item->event_date->copy()
                : \Carbon\Carbon::parse($item->event_date);
            if (! empty($item->event_time)) {
                $timeString = $item->event_time;
                if (strpos($timeString, ' ') !== false) {
                    $timeString = explode(' ', $timeString)[1];
                }
                $timeParts = explode(':', $timeString);
                $startTime->setTime((int) $timeParts[0], (int) ($timeParts[1] ?? 0), 0);
            } elseif ($startTime->format('H:i') === '00:00') {
                $startTime->setTime(9, 0);
            }
            $duration = (int) ($item->duration_minutes ?? 60);
            if ($duration < 1) {
                $duration = 60;
            }
            $endTime = $startTime->copy()->addMinutes($duration);

            return [$startTime, $endTime];
        }

        return null;
    }

    public function createEvent($item, $userId, $type = 'hearing', $createMeetingLink = false)
    {
        if (! $this->isEnabled($userId)) {
            \Log::info('Google Calendar not enabled for user', ['user_id' => $userId]);

            return null;
        }

        try {
            if (! in_array($type, ['hearing', 'timeline'], true)) {
                \Log::warning('Google Calendar createEvent skipped: unsupported type', ['type' => $type]);

                return null;
            }

            \Log::info('Setting up Google Calendar client', ['user_id' => $userId, 'type' => $type]);
            $this->setupClient($userId);

            $summary = $this->googleCalendarEventSummary($item, $type);
            $description = $this->googleCalendarEventDescription($item, $type);

            $shouldCreateMeetingLink = (bool) $createMeetingLink;

            $event = new Google_Service_Calendar_Event([
                'summary' => $summary,
                'description' => $description,
                // Store metadata in private extended properties instead of description
                'extendedProperties' => [
                    'private' => [
                        'app_type' => $type,
                        'app_id' => $item->id,
                        'app_user_id' => $userId,
                    ],
                ],
            ]);

            $window = $this->resolveGoogleEventStartEnd($item, $type);
            if ($window === null) {
                return null;
            }
            [$startTime, $endTime] = $window;

            $start = new Google_Service_Calendar_EventDateTime;
            $start->setDateTime($startTime->format('c'));
            $event->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime;
            $end->setDateTime($endTime->format('c'));
            $event->setEnd($end);

            // Add conference data (Google Meet) if requested
            if ($shouldCreateMeetingLink) {
                $conferenceData = new \Google_Service_Calendar_ConferenceData;
                $conferenceRequest = new \Google_Service_Calendar_CreateConferenceRequest;
                $conferenceRequest->setRequestId(uniqid());
                $conferenceData->setCreateRequest($conferenceRequest);
                $event->setConferenceData($conferenceData);
            }

            // Get calendar ID from settings (UPPERCASE or camelCase)
            $calendarId = Setting::where('tenant_id', $userId)
                ->whereIn('key', [SettingKey::GoogleCalendarId->value, 'googleCalendarId'])
                ->value('value') ?: 'primary';

            $calendarEvent = $this->service->events->insert($calendarId, $event, [
                'conferenceDataVersion' => $shouldCreateMeetingLink ? 1 : 0,
            ]);
            $eventId = $calendarEvent->getId();

            // Extract meeting link if conference was created
            $meetingLink = null;
            if ($shouldCreateMeetingLink && $calendarEvent->getConferenceData()) {
                $entryPoints = $calendarEvent->getConferenceData()->getEntryPoints();
                if ($entryPoints && count($entryPoints) > 0) {
                    foreach ($entryPoints as $entryPoint) {
                        if ($entryPoint->getEntryPointType() === 'video') {
                            $meetingLink = $entryPoint->getUri();
                            break;
                        }
                    }
                }
            }

            // Return event ID and meeting link
            if ($meetingLink) {
                return ['event_id' => $eventId, 'meeting_link' => $meetingLink];
            }

            return $eventId;
        } catch (\Exception $e) {
            \Log::error('Google Calendar event creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'type' => $type,
                'item_id' => $item->id ?? 'unknown',
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    public function updateEvent($eventId, $item, $userId, $type = 'hearing')
    {
        if (! $this->isEnabled($userId) || ! $eventId) {
            return false;
        }

        try {
            if (! in_array($type, ['hearing', 'timeline'], true)) {
                \Log::warning('Google Calendar updateEvent skipped: unsupported type', ['type' => $type]);

                return false;
            }

            $this->setupClient($userId);

            // Get calendar ID from settings (UPPERCASE or camelCase)
            $calendarId = Setting::where('tenant_id', $userId)
                ->whereIn('key', [SettingKey::GoogleCalendarId->value, 'googleCalendarId'])
                ->value('value') ?: 'primary';

            $event = $this->service->events->get($calendarId, $eventId);

            $summary = $this->googleCalendarEventSummary($item, $type);
            $description = $this->googleCalendarEventDescription($item, $type);

            $event->setSummary($summary);
            $event->setDescription($description);

            $window = $this->resolveGoogleEventStartEnd($item, $type);
            if ($window === null) {
                return false;
            }
            [$startTime, $endTime] = $window;

            $start = new Google_Service_Calendar_EventDateTime;
            $start->setDateTime($startTime->format('c'));
            $event->setStart($start);

            $end = new Google_Service_Calendar_EventDateTime;
            $end->setDateTime($endTime->format('c'));
            $event->setEnd($end);

            $this->service->events->update($calendarId, $eventId, $event);

            return true;
        } catch (\Exception $e) {
            \Log::error('Google Calendar event update failed: '.$e->getMessage());

            return false;
        }
    }

    public function deleteEvent($eventId, $userId)
    {
        if (! $this->isEnabled($userId) || ! $eventId) {
            return false;
        }

        try {
            $this->setupClient($userId);
            // Get calendar ID from settings (UPPERCASE or camelCase)
            $calendarId = Setting::where('tenant_id', $userId)
                ->whereIn('key', [SettingKey::GoogleCalendarId->value, 'googleCalendarId'])
                ->value('value') ?: 'primary';

            $this->service->events->delete($calendarId, $eventId);

            return true;
        } catch (\Exception $e) {
            \Log::error('Google Calendar event deletion failed: '.$e->getMessage());

            return false;
        }
    }

    public function getEvents($userId, $maxResults = 100, $timeMin = null, $timeMax = null)
    {
        // Use createdBy() for settings but keep userId for filtering
        $settingsUserId = createdBy();

        if (! $this->isEnabled($settingsUserId)) {
            return [];
        }

        try {
            $this->setupClient($settingsUserId);

            // Get calendar ID from settings (UPPERCASE or camelCase)
            $calendarId = Setting::where('tenant_id', $settingsUserId)
                ->whereIn('key', [SettingKey::GoogleCalendarId->value, 'googleCalendarId'])
                ->value('value') ?: 'primary';

            $optParams = [
                'maxResults' => $maxResults,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => $timeMin ?: date('c', strtotime('-1 month')),
            ];

            if ($timeMax) {
                $optParams['timeMax'] = $timeMax;
            }

            $results = $this->service->events->listEvents($calendarId, $optParams);
            $events = $results->getItems();

            // Get current user for filtering
            $currentUser = \App\Models\User::find($userId);

            $filteredEvents = array_filter(array_map(function ($event) use ($userId, $currentUser, $settingsUserId) {
                $start = $event->getStart()->getDateTime() ?: $event->getStart()->getDate();
                $end = $event->getEnd()->getDateTime() ?: $event->getEnd()->getDate();
                $title = $event->getSummary() ?: 'Untitled Event';
                $description = $event->getDescription() ?: '';

                // Extract metadata from extended properties to get original record info
                $originalData = $this->extractOriginalData($event, $settingsUserId);

                \Log::info('Google Calendar event processing', [
                    'event_title' => $title,
                    'user_id' => $userId,
                    'settings_user_id' => $settingsUserId,
                    'has_original_data' => ! is_null($originalData),
                ]);

                // Filter for team members: only show events for cases they are assigned to
                if ($currentUser && $currentUser->type === 'team_member') {
                    if ($originalData) {
                        $type = $originalData['type'] ?? null;
                        $recordId = $originalData['record_id'] ?? null;

                        // For team_member events, only show if it's their own assignment
                        if ($type === 'team_member') {
                            $teamMember = \App\Models\CaseTeamMember::find($recordId);
                            if (! $teamMember || $teamMember->user_id != $userId) {
                                return null;
                            }
                        } else {
                            // For other event types, check case assignment
                            $caseId = $this->getCaseIdFromEvent($originalData);
                            if (! $caseId || ! $this->isUserAssignedToCase($userId, $caseId)) {
                                return null;
                            }
                        }
                    } else {
                        return null; // Hide non-case events for team members
                    }
                }

                if ($originalData) {
                    // Use data from original database record
                    $type = $originalData['type'];
                    $color = $originalData['color'];
                    $caseTitle = $originalData['case_title'];
                    $details = $originalData['details'];
                } else {
                    // Fallback to keyword detection for external Google Calendar events
                    $type = 'google';
                    $color = '#4285f4';
                    $caseTitle = 'Google Calendar';

                    $titleLower = strtolower($title);
                    $descLower = strtolower($description);

                    if (strpos($titleLower, 'hearing') !== false || strpos($descLower, 'hearing') !== false) {
                        $type = 'hearing';
                        $color = '#3b82f6';
                        $caseTitle = 'Court Hearing';
                    } elseif (strpos($titleLower, 'task') !== false || strpos($descLower, 'task') !== false) {
                        $type = 'task';
                        $color = '#f59e0b';
                        $caseTitle = 'Task';
                    } elseif (strpos($titleLower, 'meeting') !== false || strpos($descLower, 'meeting') !== false) {
                        $type = 'timeline';
                        $color = '#10b981';
                        $caseTitle = 'Meeting';
                    } elseif (strpos($titleLower, 'team') !== false || strpos($descLower, 'team') !== false) {
                        $type = 'team_member';
                        $color = '#8b5cf6';
                        $caseTitle = 'Team Event';
                    }

                    $details = [
                        'description' => $description,
                        'location' => $event->getLocation() ?: '',
                        'calendar_source' => 'Google Calendar',
                    ];
                }

                return [
                    'id' => 'google_'.$event->getId(),
                    'title' => $title,
                    'description' => $originalData ? $originalData['clean_description'] : $description,
                    'date' => substr($start, 0, 10),
                    'time' => strpos($start, 'T') !== false ? substr($start, 11, 8) : null,
                    'end_time' => strpos($end, 'T') !== false ? substr($end, 11, 8) : null,
                    'duration' => $this->calculateDuration($start, $end),
                    'type' => $type,
                    'color' => $color,
                    'case_title' => $caseTitle,
                    'court_name' => $originalData['court_name'] ?? null,
                    'judge_name' => $originalData['judge_name'] ?? null,
                    'assigned_to' => $originalData['assigned_to'] ?? null,
                    'client_name' => $originalData['client_name'] ?? null,
                    'priority' => $originalData['priority'] ?? null,
                    'location' => $originalData['location'] ?? $event->getLocation(),
                    'status' => $originalData['status'] ?? 'active',
                    'source' => 'google',
                    'details' => $details,
                ];
            }, $events));

            return array_values($filteredEvents);
        } catch (\Exception $e) {
            \Log::error('Google Calendar events fetch failed: '.$e->getMessage());
            throw $e;
        }
    }

    public function isAuthorized($userId)
    {
        $refresh = Setting::query()
            ->where('tenant_id', $userId)
            ->where('key', 'GOOGLE_REFRESH_TOKEN')
            ->value('value');

        return is_string($refresh) && $refresh !== '';
    }

    private function extractOriginalData($event, $userId)
    {
        // First try extended properties (new method)
        $extendedProps = $event->getExtendedProperties();
        if ($extendedProps && $extendedProps->getPrivate()) {
            $privateProps = $extendedProps->getPrivate();
            $type = $privateProps['app_type'] ?? null;
            $recordId = $privateProps['app_id'] ?? null;
            $originalUserId = $privateProps['app_user_id'] ?? null;

            if ($type && $recordId) {
                $description = $event->getDescription() ?: '';

                try {
                    $data = null;
                    switch ($type) {
                        case 'hearing':
                            $data = $this->getHearingData($recordId, $description);
                            break;
                        case 'task':
                            $data = $this->getTaskData($recordId, $description);
                            break;
                        case 'timeline':
                            $data = $this->getTimelineData($recordId, $description);
                            break;
                        case 'case':
                            $data = $this->getCaseData($recordId, $description);
                            break;
                        case 'team_member':
                            $data = $this->getTeamMemberData($recordId, $description);
                            break;
                    }

                    if ($data) {
                        $data['type'] = $type;
                        $data['record_id'] = $recordId;
                    }

                    return $data;
                } catch (\Exception $e) {
                    \Log::error('Failed to fetch original data: '.$e->getMessage());
                }
            }
        }

        // Fallback to description parsing for existing events
        $description = $event->getDescription() ?: '';
        if (preg_match('/\[METADATA: type=([^,]+), id=([^,]+), user_id=([^\]]+)\]/', $description, $matches)) {
            $type = $matches[1];
            $recordId = $matches[2];
            $originalUserId = $matches[3];

            // Clean description by removing metadata
            $cleanDescription = preg_replace('/\n\n\[METADATA:.*?\]/', '', $description);

            try {
                $data = null;
                switch ($type) {
                    case 'hearing':
                        $data = $this->getHearingData($recordId, $cleanDescription);
                        break;
                    case 'task':
                        $data = $this->getTaskData($recordId, $cleanDescription);
                        break;
                    case 'timeline':
                        $data = $this->getTimelineData($recordId, $cleanDescription);
                        break;
                    case 'case':
                        $data = $this->getCaseData($recordId, $cleanDescription);
                        break;
                    case 'team_member':
                        $data = $this->getTeamMemberData($recordId, $cleanDescription);
                        break;
                }

                if ($data) {
                    $data['type'] = $type;
                    $data['record_id'] = $recordId;
                }

                return $data;
            } catch (\Exception $e) {
                \Log::error('Failed to fetch original data: '.$e->getMessage());

            }
        }

        return null;
    }

    private function getCaseIdFromEvent($originalData)
    {
        $type = $originalData['type'] ?? null;
        $recordId = $originalData['record_id'] ?? null;

        if (! $recordId) {
            return null;
        }

        switch ($type) {
            case 'case':
                return $recordId;
            case 'task':
                $task = \App\Models\Task::find($recordId);

                return $task ? $task->case_id : null;
            case 'hearing':
                $hearing = \App\Models\Hearing::find($recordId);

                return $hearing ? $hearing->case_id : null;
            case 'timeline':
                $timeline = \App\Models\CaseTimeline::find($recordId);

                return $timeline ? $timeline->case_id : null;
            case 'team_member':
                $teamMember = \App\Models\CaseTeamMember::find($recordId);

                return $teamMember ? $teamMember->case_id : null;
        }

        return null;
    }

    private function isUserAssignedToCase($userId, $caseId)
    {
        return \App\Models\CaseTeamMember::where('case_id', $caseId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();
    }

    private function getHearingData($id, $cleanDescription)
    {
        $hearing = \App\Models\Hearing::with(['case.client', 'court'])->find($id);
        if (! $hearing) {
            return null;
        }

        return [
            'type' => 'hearing',
            'color' => '#3b82f6',
            'case_title' => $hearing->case->title ?? 'No Case',
            'client_name' => $hearing->case->client->name ?? 'No Client',
            'court_name' => $hearing->court->name ?? '',
            'judge_name' => '',
            'location' => $hearing->court->address ?? '',
            'status' => $hearing->status,
            'clean_description' => $cleanDescription,
            'details' => [
                'hearing_id' => $hearing->hearing_id,
                'description' => $cleanDescription,
                'notes' => $hearing->notes,
                'outcome' => $hearing->outcome,
                'status' => $hearing->status,
                'case_number' => $hearing->case->case_number ?? '',
                'client_details' => [
                    'name' => $hearing->case->client->name ?? '',
                    'email' => $hearing->case->client->email ?? '',
                    'phone' => $hearing->case->client->phone ?? '',
                ],
            ],
        ];
    }

    private function getTaskData($id, $cleanDescription)
    {
        $task = \App\Models\Task::with(['case.client', 'assignedUser', 'taskStatus'])->find($id);
        if (! $task) {
            return null;
        }

        $statusLabel = $task->taskStatus ? (string) $task->taskStatus->name : '';

        return [
            'type' => 'task',
            'color' => $this->getTaskColorForTask($task),
            'case_title' => $task->case->title ?? 'No Case',
            'client_name' => $task->case->client->name ?? 'No Client',
            'assigned_to' => $task->assignedUser->name ?? 'Unassigned',
            'priority' => $task->priority instanceof TaskPriority ? $task->priority->value : $task->priority,
            'status' => $statusLabel,
            'clean_description' => $cleanDescription,
            'details' => [
                'task_id' => $task->task_id,
                'description' => $cleanDescription,
                'notes' => $task->notes,
                'status' => $statusLabel,
                'priority' => $task->priority instanceof TaskPriority ? $task->priority->value : $task->priority,
                'estimated_duration' => $task->estimated_duration,
                'case_number' => $task->case->case_number ?? '',
                'client_details' => [
                    'name' => $task->case->client->name ?? '',
                    'email' => $task->case->client->email ?? '',
                    'phone' => $task->case->client->phone ?? '',
                ],
            ],
        ];
    }

    private function getTimelineData($id, $cleanDescription)
    {
        $timeline = \App\Models\CaseTimeline::with(['case.client', 'eventType'])->find($id);
        if (! $timeline) {
            return null;
        }

        return [
            'type' => 'timeline',
            'color' => $timeline->is_completed ? '#10b981' : '#f59e0b',
            'case_title' => $timeline->case->title ?? 'No Case',
            'client_name' => $timeline->case->client->name ?? 'No Client',
            'location' => $timeline->location ?? '',
            'status' => $timeline->status ?? 'active',
            'clean_description' => $cleanDescription,
            'details' => [
                'description' => $cleanDescription,
                'location' => $timeline->location,
                'participants' => $timeline->participants,
                'status' => $timeline->status,
                'event_type' => $timeline->eventType->name ?? $timeline->event_type,
                'is_completed' => $timeline->is_completed,
                'case_number' => $timeline->case->case_number ?? '',
                'client_details' => [
                    'name' => $timeline->case->client->name ?? '',
                    'email' => $timeline->case->client->email ?? '',
                    'phone' => $timeline->case->client->phone ?? '',
                ],
            ],
        ];
    }

    private function getCaseData($id, $cleanDescription)
    {
        $case = \App\Models\CaseModel::with(['client', 'caseType', 'caseStatus'])->find($id);
        if (! $case) {
            return null;
        }

        return [
            'type' => 'case',
            'color' => '#6366f1',
            'case_title' => $case->title,
            'client_name' => $case->client->name ?? 'No Client',
            'status' => $case->caseStatus->name ?? $case->status,
            'clean_description' => $cleanDescription,
            'details' => [
                'case_number' => $case->case_number,
                'description' => $cleanDescription,
                'status' => $case->caseStatus->name ?? $case->status,
                'case_type' => $case->caseType->name ?? '',
                'filing_date' => $case->filing_date,
                'client_details' => [
                    'name' => $case->client->name ?? '',
                    'email' => $case->client->email ?? '',
                    'phone' => $case->client->phone ?? '',
                    'address' => $case->client->address ?? '',
                ],
            ],
        ];
    }

    private function getTeamMemberData($id, $cleanDescription)
    {
        $teamMember = \App\Models\CaseTeamMember::with(['case.client', 'user'])->find($id);
        if (! $teamMember) {
            return null;
        }

        return [
            'type' => 'team_member',
            'color' => '#8b5cf6',
            'case_title' => $teamMember->case->title ?? 'No Case',
            'client_name' => $teamMember->case->client->name ?? 'No Client',
            'assigned_to' => $teamMember->user->name ?? 'Unknown',
            'status' => $teamMember->status,
            'clean_description' => $cleanDescription,
            'details' => [
                'user_name' => $teamMember->user->name ?? 'Unknown',
                'user_email' => $teamMember->user->email ?? '',
                'assigned_date' => $teamMember->assigned_date,
                'role' => $teamMember->role ?? 'Team Member',
                'status' => $teamMember->status,
                'case_number' => $teamMember->case->case_number ?? '',
                'client_details' => [
                    'name' => $teamMember->case->client->name ?? '',
                    'email' => $teamMember->case->client->email ?? '',
                    'phone' => $teamMember->case->client->phone ?? '',
                ],
            ],
        ];
    }

    private function getTaskColorForTask(\App\Models\Task $task): string
    {
        if ($task->relationLoaded('taskStatus') && $task->taskStatus) {
            if ($task->taskStatus->is_completed) {
                return '#10b981';
            }
            if (! empty($task->taskStatus->color)) {
                return $task->taskStatus->color;
            }
        }

        $priorityColors = [
            'critical' => '#dc2626',
            'high' => '#ea580c',
            'medium' => '#d97706',
            'low' => '#65a30d',
        ];

        $p = $task->priority instanceof TaskPriority ? $task->priority->value : (string) $task->priority;

        return $priorityColors[$p] ?? '#6b7280';
    }

    private function calculateDuration($start, $end)
    {
        if (! $start || ! $end) {
            return null;
        }

        try {
            $startTime = new \DateTime($start);
            $endTime = new \DateTime($end);
            $diff = $startTime->diff($endTime);

            $minutes = ($diff->h * 60) + $diff->i;

            return $minutes > 0 ? $minutes : 60; // Default to 60 minutes if no duration
        } catch (\Exception $e) {
            return 60; // Default duration
        }
    }

    public function getAuthorizationUrl($userId)
    {
        try {
            $client = new Google_Client;
            $this->configureOAuthClient($client, (string) $userId);
            $client->setPrompt('select_account consent');

            return $client->createAuthUrl();
        } catch (\Exception $e) {
            \Log::error('Failed to generate Google Calendar authorization URL: '.$e->getMessage());

            return null;
        }
    }
}
