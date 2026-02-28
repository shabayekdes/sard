<?php

namespace App\Http\Controllers\Settings;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Currency;
use App\Models\PaymentSetting;
use App\Models\Webhook;
use App\Models\CompanySetting;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Models\TenantEmailTemplate;
use App\Models\TaxRate;
use App\Http\Resources\CurrencyResource;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the main settings page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Get system settings using helper function
        $systemSettings = Settings::sanitize();
        $currencies = CurrencyResource::collection(
            Currency::where('status', true)->get()
        )->resolve();
        $paymentSettings = sanitizeSettingsForUi(
            PaymentSetting::getUserSettings(createdBy()),
            createdBy()
        );
        $webhooks = Webhook::where('user_id', auth()->id())->get();
        $companySettings = CompanySetting::where('tenant_id', createdBy())->get();
        $taxRates = TaxRate::where('is_active', true)
            ->orderByRaw("JSON_EXTRACT(name, '$.en')")
            ->get(['id', 'name', 'rate']);
        $countries = Country::where('is_active', true)
            ->orderByRaw("JSON_EXTRACT(name, '$.en')")
            ->get(['country_code', 'name'])
            ->map(function ($country) {
                return [
                    'value' => (string) $country->country_code,
                    'label' => $country->name,
                ];
            });

        // Get Slack settings
        // $slackSettings = SlackSettingsService::getSettings(auth()->id());

        // Get notification templates for Slack settings
        $notificationTemplates = NotificationTemplate::select('id', 'name')->get();

        $emailTemplates = [];
        if (Auth::user()->type === 'company' && Auth::user()->tenant_id) {
            $locale = app()->getLocale();
            $emailTemplatesQuery = EmailTemplate::get();
            $tenantSettings = TenantEmailTemplate::where('tenant_id', Auth::user()->tenant_id)->get()->keyBy('template_id');

            $emailTemplates = $emailTemplatesQuery->map(function (EmailTemplate $template) use ($tenantSettings) {
                $locale = app()->getLocale();
                $name = $template->getTranslation('name', $locale, false)
                    ?: $template->getTranslation('name', 'en', false);
                $from = $template->getTranslation('from', $locale, false)
                    ?: $template->getTranslation('from', 'en', false);

                // Get or create tenant setting for this template
                $tenantSetting = $tenantSettings->get($template->id);

                // If no record exists, create one as disabled
                if (!$tenantSetting) {
                    $tenantSetting = TenantEmailTemplate::create([
                        'template_id' => $template->id,
                        'tenant_id' => Auth::user()->tenant_id,
                        'is_active' => 0
                    ]);
                }

                return [
                    'id' => $template->id,
                    'name' => $name,
                    'is_active' => $tenantSetting->is_active,
                    'template' => [
                        'id' => $template->id,
                        'name' => $name,
                        'from' => $from
                    ]
                ];
            });
        }


        return Inertia::render('settings/index', [
            'systemSettings' => $systemSettings,
            'cacheSize' => getCacheSize(),
            'currencies' => $currencies,
            'timezones' => config('timezones'),
            'dateFormats' => config('dateformat'),
            'timeFormats' => config('timeformat'),
            'paymentSettings' => $paymentSettings,
            'webhooks' => $webhooks,
            'companySettings' => $companySettings,
            'taxRates' => $taxRates,
            'countries' => $countries,
            'emailTemplates' => $emailTemplates,
            // 'slackSettings' => $slackSettings,
            'notificationTemplates' => $notificationTemplates,
        ]);
    }

    public function storeCompanySetting(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255'
        ]);

        CompanySetting::create([
            'setting_key' => $validated['key'],
            'setting_value' => $validated['value'],
            'description' => $validated['description'],
            'category' => $validated['category'] ?? 'General',
            'tenant_id' => createdBy()
        ]);

        return redirect()->back()->with('success', 'Company setting created successfully.');
    }

    public function updateCompanySetting(Request $request, $id)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255'
        ]);

        $setting = CompanySetting::where('id', $id)
            ->where('tenant_id', createdBy())
            ->firstOrFail();

        $setting->update([
            'setting_key' => $validated['key'],
            'setting_value' => $validated['value'],
            'description' => $validated['description'],
            'category' => $validated['category'] ?? 'General'
        ]);

        return redirect()->back()->with('success', 'Company setting updated successfully.');
    }

    public function destroyCompanySetting($id)
    {
        $setting = CompanySetting::where('id', $id)
            ->where('tenant_id', createdBy())
            ->firstOrFail();

        $setting->delete();

        return redirect()->back()->with('success', 'Company setting deleted successfully.');
    }

    /**
     * Get current settings as JSON for API calls
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings()
    {
        $settings = Settings::all();
        return response()->json([
            'settings' => $settings
        ]);
    }
}
