<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Facades\Settings;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IntegrationsController extends BaseController
{
    public function index()
    {
        $tenantId = tenant('id');

        return Inertia::render('integrations/index', [
            'googleCalendarEnabled' => Settings::boolean('GOOGLE_CALENDAR_ENABLED', false),
            'googleCalendarId' => $tenantId
                ? (Setting::query()
                    ->where('tenant_id', $tenantId)
                    ->where('key', SettingKey::GoogleCalendarId->value)
                    ->value('value') ?? '')
                : '',
        ]);
    }

    public function updateGoogleCalendar(Request $request): RedirectResponse
    {
        $tenantId = tenant('id');
        abort_if($tenantId === null, 403);

        if (! Settings::boolean('GOOGLE_CALENDAR_ENABLED', false)) {
            return redirect()
                ->route('integrations.index')
                ->with('error', __('Google Calendar integration is not enabled.'));
        }

        $validated = $request->validate([
            'google_calendar_id' => 'required|string|max:1024',
        ]);

        Setting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => SettingKey::GoogleCalendarId->value],
            ['value' => $validated['google_calendar_id'], 'group' => 'integrations'],
        );

        return redirect()
            ->route('integrations.index')
            ->with('success', __('Google Calendar selection saved successfully.'));
    }

    /**
     * Reset Google Calendar integration keys to defaults (matches database/seeders/data/settings.php).
     */
    public function disconnectGoogleCalendar(): RedirectResponse
    {
        $tenantId = tenant('id');
        abort_if($tenantId === null, 403);

        if (! Settings::boolean('GOOGLE_CALENDAR_ENABLED', false)) {
            return redirect()->route('integrations.index');
        }

        $defaults = [
            'GOOGLE_CALENDAR_ENABLED' => false,
            'GOOGLE_TOKEN' => '',
            'GOOGLE_REFRESH_TOKEN' => '',
            'GOOGLE_TOKEN_EXPIRES_AT' => '',
            SettingKey::GoogleCalendarId->value => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['tenant_id' => $tenantId, 'key' => $key],
                ['value' => $value, 'group' => 'integrations'],
            );
        }

        return redirect()
            ->route('integrations.index')
            ->with('success', __('Google Calendar disconnected successfully.'));
    }
}
