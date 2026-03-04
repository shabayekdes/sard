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
            Currency::where('status', 'active')->get()
        )->resolve();
        $paymentSettings = sanitizeSettingsForUi(
            PaymentSetting::getUserSettings(createdBy()),
            createdBy()
        );
        $webhooks = Webhook::where('user_id', auth()->id())->get();
        $taxRates = TaxRate::where('status', 'active')
            ->orderByRaw("JSON_EXTRACT(name, '$.en')")
            ->get(['id', 'name', 'rate']);
        $countries = Country::where('status', 'active')
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
                        'status' => 'inactive'
                    ]);
                }

                return [
                    'id' => $template->id,
                    'name' => $name,
                    'is_active' => $tenantSetting->status === 'active',
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
            'taxRates' => $taxRates,
            'countries' => $countries,
            'emailTemplates' => $emailTemplates,
            // 'slackSettings' => $slackSettings,
            'notificationTemplates' => $notificationTemplates,
        ]);
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
