<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\Setting;
use App\Services\GoogleCalendarService;
use Google_Service_Calendar as Calendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;

class GoogleCalendarController extends Controller
{
    /** Max age of OAuth state payload in seconds (replay window). */
    private const OAUTH_STATE_MAX_AGE = 600;

    public function __construct(
        protected GoogleCalendarService $calendarService
    )
    {
    }

    public function getEvents(Request $request)
    {
        try {
            $events = $this->calendarService->getEvents(auth()->id(), $request->get('maxResults', 50));

            return response()->json([
                'success' => true,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'events' => [],
            ]);
        }
    }

    /**
     * List calendars accessible to the connected Google account (OAuth integration).
     */
    public function listCalendars(): JsonResponse
    {
        $tenantId = createdBy();
        if ($tenantId === null) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        if (! Settings::boolean('GOOGLE_CALENDAR_ENABLED', false)) {
            return response()->json([
                'success' => false,
                'message' => __('Google Calendar integration is not enabled.'),
            ], 422);
        }

        $refreshToken = Setting::query()
            ->where('tenant_id', $tenantId)
            ->where('key', 'GOOGLE_REFRESH_TOKEN')
            ->value('value');

        if ($refreshToken === null || $refreshToken === '') {
            return response()->json([
                'success' => false,
                'message' => __('Reconnect Google Calendar to load your calendars.'),
            ], 422);
        }

        try {
            $client = $this->calendarService->createRefreshedOAuthClient((string) $tenantId);
            $service = new Calendar($client);
            $calendars = [];
            $pageToken = null;

            do {
                $params = ['maxResults' => 250];
                if ($pageToken !== null) {
                    $params['pageToken'] = $pageToken;
                }
                $list = $service->calendarList->listCalendarList($params);
                foreach ($list->getItems() ?? [] as $entry) {
                    $calendars[] = [
                        'id' => $entry->getId(),
                        'summary' => $entry->getSummary() ?: $entry->getId(),
                        'primary' => (bool) $entry->getPrimary(),
                    ];
                }
                $pageToken = $list->getNextPageToken();
            } while ($pageToken !== null);

            return response()->json([
                'success' => true,
                'calendars' => $calendars,
            ]);
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $isToken = str_contains($message, 'Failed to refresh Google access token')
                || str_contains(strtolower($message), 'invalid_grant');

            \Log::warning('Google Calendar listCalendars failed', [
                'tenant_id' => $tenantId,
                'error' => $message,
            ]);

            return response()->json([
                'success' => false,
                'message' => $message,
            ], $isToken ? 401 : 500);
        }
    }

    public function syncEvents(Request $request)
    {
        try {
            $isEnabled = $this->calendarService->isEnabled(createdBy());
            $isAuthorized = $this->calendarService->isAuthorized(createdBy());

            \Log::info('Google Calendar check', [
                'user_id' => createdBy(),
                'isEnabled' => $isEnabled,
                'isAuthorized' => $isAuthorized,
            ]);

            if (!$isEnabled || !$isAuthorized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Calendar is not connected. Connect Google from Integrations and ensure the integration is enabled.',
                    'needsConfig' => true,
                    'debug' => [
                        'isEnabled' => $isEnabled,
                        'isAuthorized' => $isAuthorized,
                    ],
                ]);
            }

            $events = $this->calendarService->getEvents(auth()->id(), 100);

            return response()->json([
                'success' => true,
                'message' => 'Calendar events synchronized successfully',
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync calendar events: ' . $e->getMessage(),
                'needsAuth' => str_contains($e->getMessage(), 'not authorized') || str_contains($e->getMessage(), 'expired'),
            ]);
        }
    }

    /**
     * Redirect the user to Google's OAuth consent screen (Laravel Socialite).
     */
    public function authorizeGoogleCalendar(Request $request)
    {
        try {
            $payload = [
                'tenant' => tenant('id'),        // or however you obtain it
                'ts' => now()->timestamp,    // optional: for expiry checks
                'redirectUri' => route('integrations.index'),
            ];

            // encrypt JSON, then make it URL-safe (base64url)
            $enc = Crypt::encryptString(json_encode($payload, JSON_UNESCAPED_SLASHES));
            $state = rtrim(strtr(base64_encode($enc), '+/', '-_'), '='); // base64url

            return Socialite::driver('google')
                ->scopes([Calendar::CALENDAR])
                ->with([
                    'state' => $state,
                    'access_type' => 'offline',           // <- required for refresh_token
                    'prompt' => 'consent select_account', // <- show consent so Google can return refresh_token
                    'include_granted_scopes' => 'true',   // optional, incremental auth
                ])     // <-- put our signed blob in state
                ->stateless()
                ->redirect();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to authorize Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Handle Google OAuth callback and store tokens for the workspace.
     */
    public function callback(Request $request)
    {
        try {
            // 1) Recover & validate state
            $stateRaw = (string)$request->query('state', '');
            if ($stateRaw === '') abort(403, 'Missing OAuth state.');

            // reverse base64url -> decrypt -> json
            $b64 = strtr($stateRaw, '-_', '+/');
            $b64 .= str_repeat('=', (4 - strlen($b64) % 4) % 4);
            $enc = base64_decode($b64, true);
            if ($enc === false) {
                abort(403, 'Invalid OAuth state (b64).');
            }

            try {
                $json = Crypt::decryptString($enc);
            } catch (\Throwable $e) {
                abort(403, 'Invalid or tampered OAuth state (decrypt).');
            }

            $payload = json_decode($json, true);
            if (!is_array($payload)) {
                abort(403, 'Invalid OAuth state (json).');
            }
            // Optional: check expiry (e.g., 15 min)
            $maxAge = 15 * 60;
            if (!isset($payload['ts']) || (now()->timestamp - $payload['ts']) > $maxAge) {
                abort(403, 'Expired OAuth state.');
            }

            $tenantId = $payload['tenant'] ?? null;
            if (!$tenantId) abort(403, 'Missing tenant in OAuth state.');

            // If you use a tenancy package, initialize here:
            // tenancy()->initialize($tenantId);

            // 2) Finish Socialite stateless flow
            $providerUser = Socialite::driver('google')->stateless()->user();

            $tenant = \App\Models\Tenant::findOrFail($tenantId);

            $tenant->run(function () use ($providerUser, $tenant) {
                Setting::updateOrCreate([
                    'tenant_id' => $tenant->id,
                    'key' => 'GOOGLE_CALENDAR_ENABLED',
                ], [
                    'value' => true,
                    'group' => 'integrations',
                ]);
                Setting::updateOrCreate([
                    'tenant_id' => $tenant->id,
                    'key' => 'GOOGLE_TOKEN',
                ], [
                    'value' => $providerUser->token,
                    'group' => 'integrations',
                ]);
                Setting::updateOrCreate([
                    'tenant_id' => $tenant->id,
                    'key' => 'GOOGLE_REFRESH_TOKEN',
                ], [
                    'value' => $providerUser->refreshToken,
                    'group' => 'integrations',
                ]);
                Setting::updateOrCreate([
                    'tenant_id' => $tenant->id,
                    'key' => 'GOOGLE_TOKEN_EXPIRES_AT',
                ], [
                    'value' => now()->addSeconds($providerUser->expiresIn)->toDateTimeString(),
                    'group' => 'integrations',
                ]);
            });

            return redirect($payload['redirectUri']);
        } catch (\Throwable $e) {
            abort(403, $e->getMessage());
        }
    }
}
