<?php

namespace App\Http\Controllers\Settings;

use App\Enums\SettingKey;
use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\TenantNotificationTemplate;
use App\Services\StorageConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SystemSettingsController extends Controller
{
    /**
     * Update the system settings.
     *
     * Handles system-wide configuration including:
     * - Language and localization settings
     * - Date/time formats and timezone
     * - Email verification requirements
     * - Landing page enable/disable toggle
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'defaultCountry' => 'nullable|exists:countries,country_code',
                'defaultLanguage' => 'nullable|string',
                'dateFormat' => 'nullable|string',
                'timeFormat' => 'nullable|string',
                'calendarStartDay' => 'nullable|string',
                'defaultTimezone' => 'nullable|string',
                'defaultTaxRate' => 'nullable|numeric|min:0|max:100',
                'emailVerification' => 'nullable|boolean',
                'landingPageEnabled' => 'nullable|boolean',
            ]);

            if (empty($validated)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($validated as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), $value);
            }

            return redirect()->back()->with('success', __('System settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update system settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the brand settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBrand(Request $request)
    {
        try {
            $validated = $request->validate([
                'logoDark' => 'nullable|string',
                'logoLight' => 'nullable|string',
                'favicon' => 'nullable|string',
                'titleTextEn' => 'nullable|string|max:255',
                'titleTextAr' => 'nullable|string|max:255',
                'footerTextEn' => 'nullable|string|max:500',
                'footerTextAr' => 'nullable|string|max:500',
                'themeColor' => 'nullable|string|in:blue,green,purple,orange,red,custom',
                'customColor' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'sidebarVariant' => 'nullable|string|in:inset,floating,minimal',
                'sidebarStyle' => 'nullable|string|in:plain,colored,gradient',
                'layoutDirection' => 'nullable|string|in:left,right,ltr,rtl',
                'themeMode' => 'nullable|string|in:light,dark,system',
            ]);

            if (empty($validated)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($validated as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), $value);
            }

            return redirect()->back()->with('success', __('Brand settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update brand settings: :error', ['error' => $e->getMessage()]));
        }
    }



    /**
     * Update the recaptcha settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRecaptcha(Request $request)
    {
        try {
            $validated = $request->validate([
                'recaptchaEnabled' => 'nullable|boolean',
                'recaptchaVersion' => 'nullable|in:v2,v3',
                'recaptchaSiteKey' => 'nullable|string',
                'recaptchaSecretKey' => 'nullable|string',
            ]);

            if (empty($validated)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($validated as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), is_bool($value) ? ($value ? '1' : '0') : $value);
            }

            return redirect()->back()->with('success', __('ReCaptcha settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update ReCaptcha settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the chatgpt settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateChatgpt(Request $request)
    {
        try {
            $validated = $request->validate([
                'chatgptKey' => 'nullable|string',
                'chatgptModel' => 'nullable|string',
            ]);

            if (empty($validated)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($validated as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), $value);
            }

            return redirect()->back()->with('success', __('Chat GPT settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Chat GPT settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the storage settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStorage(Request $request)
    {
        try {
            $validated = $request->validate([
                'storage_type' => 'nullable|in:local,s3,wasabi',
                'allowedFileTypes' => 'nullable|string',
                'maxUploadSize' => 'nullable|numeric|min:1',
                'awsAccessKeyId' => 'nullable|string',
                'awsSecretAccessKey' => 'nullable|string',
                'awsDefaultRegion' => 'nullable|string',
                'awsBucket' => 'nullable|string',
                'awsUrl' => 'nullable|string',
                'awsEndpoint' => 'nullable|string',
                'wasabiAccessKey' => 'nullable|string',
                'wasabiSecretKey' => 'nullable|string',
                'wasabiRegion' => 'nullable|string',
                'wasabiBucket' => 'nullable|string',
                'wasabiUrl' => 'nullable|string',
                'wasabiRoot' => 'nullable|string',
            ]);

            $settings = [];
            if (array_key_exists('storage_type', $validated)) {
                $settings['storage_type'] = $validated['storage_type'];
            }
            if (array_key_exists('allowedFileTypes', $validated)) {
                $settings['storage_file_types'] = $validated['allowedFileTypes'];
            }
            if (array_key_exists('maxUploadSize', $validated)) {
                $settings['storage_max_upload_size'] = $validated['maxUploadSize'];
            }
            if (array_key_exists('awsAccessKeyId', $validated)) {
                $settings['aws_access_key_id'] = $validated['awsAccessKeyId'];
            }
            if (array_key_exists('awsSecretAccessKey', $validated)) {
                $settings['aws_secret_access_key'] = $validated['awsSecretAccessKey'];
            }
            if (array_key_exists('awsDefaultRegion', $validated)) {
                $settings['aws_default_region'] = $validated['awsDefaultRegion'];
            }
            if (array_key_exists('awsBucket', $validated)) {
                $settings['aws_bucket'] = $validated['awsBucket'];
            }
            if (array_key_exists('awsUrl', $validated)) {
                $settings['aws_url'] = $validated['awsUrl'];
            }
            if (array_key_exists('awsEndpoint', $validated)) {
                $settings['aws_endpoint'] = $validated['awsEndpoint'];
            }
            if (array_key_exists('wasabiAccessKey', $validated)) {
                $settings['wasabi_access_key'] = $validated['wasabiAccessKey'];
            }
            if (array_key_exists('wasabiSecretKey', $validated)) {
                $settings['wasabi_secret_key'] = $validated['wasabiSecretKey'];
            }
            if (array_key_exists('wasabiRegion', $validated)) {
                $settings['wasabi_region'] = $validated['wasabiRegion'];
            }
            if (array_key_exists('wasabiBucket', $validated)) {
                $settings['wasabi_bucket'] = $validated['wasabiBucket'];
            }
            if (array_key_exists('wasabiUrl', $validated)) {
                $settings['wasabi_url'] = $validated['wasabiUrl'];
            }
            if (array_key_exists('wasabiRoot', $validated)) {
                $settings['wasabi_root'] = $validated['wasabiRoot'];
            }

            if (empty($settings)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($settings as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper($key), $value);
            }

            // Clear storage config cache
            StorageConfigService::clearCache();


            return redirect()->back()->with('success', __('Storage settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update storage settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the cookie settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCookie(Request $request)
    {
        try {
            $urlOrEmailRule = function ($attribute, $value, $fail) {
                if (!filter_var($value, FILTER_VALIDATE_URL) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail(__('Contact us URL must be a valid URL or email address.'));
                }
            };

            $validated = $request->validate([
                'enableLogging' => 'nullable|boolean',
                'strictlyNecessaryCookies' => 'nullable|boolean',
                'cookieTitleEn' => 'nullable|string|max:255',
                'cookieTitleAr' => 'nullable|string|max:255',
                'strictlyCookieTitleEn' => 'nullable|string|max:255',
                'strictlyCookieTitleAr' => 'nullable|string|max:255',
                'cookieDescriptionEn' => 'nullable|string',
                'cookieDescriptionAr' => 'nullable|string',
                'strictlyCookieDescriptionEn' => 'nullable|string',
                'strictlyCookieDescriptionAr' => 'nullable|string',
                'contactUsDescriptionEn' => 'nullable|string',
                'contactUsDescriptionAr' => 'nullable|string',
                'contactUsUrlEn' => ['nullable', 'string', 'max:255', $urlOrEmailRule],
                'contactUsUrlAr' => ['nullable', 'string', 'max:255', $urlOrEmailRule],
                'cookieTitle' => 'nullable|string|max:255',
                'strictlyCookieTitle' => 'nullable|string|max:255',
                'cookieDescription' => 'nullable|string',
                'strictlyCookieDescription' => 'nullable|string',
                'contactUsDescription' => 'nullable|string',
                'contactUsUrl' => ['nullable', 'string', 'max:255', $urlOrEmailRule],
            ]);

            $settings = $validated;
            if (array_key_exists('cookieTitleEn', $validated)) {
                $settings['cookieTitle'] = $validated['cookieTitleEn'];
            }
            if (array_key_exists('strictlyCookieTitleEn', $validated)) {
                $settings['strictlyCookieTitle'] = $validated['strictlyCookieTitleEn'];
            }
            if (array_key_exists('cookieDescriptionEn', $validated)) {
                $settings['cookieDescription'] = $validated['cookieDescriptionEn'];
            }
            if (array_key_exists('strictlyCookieDescriptionEn', $validated)) {
                $settings['strictlyCookieDescription'] = $validated['strictlyCookieDescriptionEn'];
            }
            if (array_key_exists('contactUsDescriptionEn', $validated)) {
                $settings['contactUsDescription'] = $validated['contactUsDescriptionEn'];
            }
            if (array_key_exists('contactUsUrlEn', $validated)) {
                $settings['contactUsUrl'] = $validated['contactUsUrlEn'];
            }

            if (empty($settings)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($settings as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), is_bool($value) ? ($value ? '1' : '0') : $value);
            }

            return redirect()->back()->with('success', __('Cookie settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update cookie settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the SEO settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSeo(Request $request)
    {
        try {
            $validated = $request->validate([
                'metaKeywords' => 'nullable|string|max:255',
                'metaDescription' => 'nullable|string|max:160',
                'metaImage' => 'nullable|string',
            ]);

            if (empty($validated)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($validated as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), $value);
            }

            return redirect()->back()->with('success', __('SEO settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update SEO settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the Google Calendar settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGoogleCalendar(Request $request)
    {
        try {
            $validated = $request->validate([
                'googleCalendarEnabled' => 'nullable|boolean',
                'googleCalendarId' => 'nullable|string|max:255',
                'googleCalendarJson' => 'nullable|file|mimes:json|max:2048',
            ]);

            $userId = createdBy();
            $settings = [];
            if (array_key_exists('googleCalendarEnabled', $validated)) {
                $settings['googleCalendarEnabled'] = $validated['googleCalendarEnabled'];
            }
            if (array_key_exists('googleCalendarId', $validated)) {
                $settings['googleCalendarId'] = $validated['googleCalendarId'];
            }

            $credentialsChanged = false;
            if (isset($settings['googleCalendarId']) && $settings['googleCalendarId'] !== (getSetting(SettingKey::GoogleCalendarId->value, '', $userId) ?: getSetting('googleCalendarId', '', $userId))) {
                $credentialsChanged = true;
            }

            // Handle JSON file upload
            if ($request->hasFile('googleCalendarJson')) {
                $credentialsChanged = true;
                
                // Delete existing JSON file if it exists
                $existingPath = getSetting(SettingKey::GoogleCalendarJsonPath->value, null, $userId) ?: getSetting('googleCalendarJsonPath', null, $userId);
                if ($existingPath && \Storage::disk('public')->exists($existingPath)) {
                    \Storage::disk('public')->delete($existingPath);
                }
                
                $file = $request->file('googleCalendarJson');
                $path = $file->store('google-calendar', 'public');
                $settings['googleCalendarJsonPath'] = $path;
            }

            if ($credentialsChanged) {
                $settings['is_googlecalendar_sync'] = '0';
            }

            if (empty($settings)) {
                return redirect()->back()->with('info', __('No changes to save.'));
            }

            foreach ($settings as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), is_bool($value) ? ($value ? '1' : '0') : $value, $userId);
            }

            return redirect()->back()->with('success', __('Google Calendar settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Google Calendar settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Sync Google Calendar to test credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncGoogleCalendar(Request $request)
    {
        try {
            $userId = createdBy();
            $settings = settings($userId);
            $enabled = $settings[SettingKey::GoogleCalendarEnabled->value] ?? $settings['googleCalendarEnabled'] ?? null;
            $calendarId = $settings[SettingKey::GoogleCalendarId->value] ?? $settings['googleCalendarId'] ?? '';
            $jsonPathKey = $settings[SettingKey::GoogleCalendarJsonPath->value] ?? $settings['googleCalendarJsonPath'] ?? '';

            \Log::info('Google Calendar sync attempt', [
                'user_id' => $userId,
                'settings' => [
                    'googleCalendarEnabled' => $enabled !== null ? 'set' : 'not_set',
                    'googleCalendarId' => !empty($calendarId) ? 'set' : 'empty',
                    'googleCalendarJsonPath' => !empty($jsonPathKey) ? 'set' : 'empty'
                ]
            ]);

            if (!($enabled === true || $enabled === '1')) {
                return redirect()->back()->withErrors(['error' => __('Google Calendar integration is not enabled.')]);
            }

            if (trim((string) $calendarId) === '') {
                return redirect()->back()->withErrors(['error' => __('Google Calendar ID is not configured.')]);
            }

            if (trim((string) $jsonPathKey) === '') {
                return redirect()->back()->withErrors(['error' => __('Google Calendar service account JSON is not uploaded.')]);
            }

            // Get the JSON file path
            $jsonPath = storage_path('app/public/' . $jsonPathKey);
            
            if (!file_exists($jsonPath)) {
                throw new \Exception('Service account JSON file not found.');
            }

            // Validate JSON file
            $jsonContent = file_get_contents($jsonPath);
            $credentials = json_decode($jsonContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file format.');
            }

            if (!isset($credentials['type']) || $credentials['type'] !== 'service_account') {
                throw new \Exception('Invalid service account credentials.');
            }

            // Check if Google Client library is installed
            if (!class_exists('\Google_Client')) {
                throw new \Exception('Google Client library is not installed. Please run: composer require google/apiclient');
            }

            // Test Google Calendar API connection
            $client = new \Google_Client();
            $client->setAuthConfig($jsonPath);
            $client->addScope(\Google_Service_Calendar::CALENDAR_READONLY);
            
            $service = new \Google_Service_Calendar($client);
            
            // Test by fetching calendar info
            try {
                $calendar = $service->calendars->get($calendarId);
              
                if (!$calendar) {
                    throw new \Exception('Unable to access the specified calendar.');
                }
                // Store sync test success status
                Settings::update(SettingKey::IsGoogleCalendarSync->value, '1', $userId);
            } catch (\Google_Service_Exception $calendarException) {
                // Handle specific calendar access errors
                $errorCode = $calendarException->getCode();
                if ($errorCode === 404) {
                    throw new \Exception('Calendar not found. Please check your Google Calendar ID.');
                } elseif ($errorCode === 403) {
                    throw new \Exception('Access denied. Please ensure the service account has access to this calendar.');
                } else {
                    throw new \Exception('Calendar access error: ' . $calendarException->getMessage());
                }
            }
            
            
            
            return redirect()->back()->with('success', __('Google Calendar sync test completed successfully. Connected to: :name', ['name' => $calendar->getSummary()]));
        } catch (\Google_Service_Exception $e) {
            // Clear sync test status on failure
            Settings::update(SettingKey::IsGoogleCalendarSync->value, '0', $userId);

            $errorCode = $e->getCode();
            $errorMessage = 'Google API Error: ' . $e->getMessage();

            // Provide more specific error messages based on error codes
            if ($errorCode === 404) {
                $errorMessage = 'Calendar not found. Please verify your Google Calendar ID is correct.';
            } elseif ($errorCode === 403) {
                $errorMessage = 'Access denied. Please ensure the service account has proper permissions for this calendar.';
            } elseif ($errorCode === 401) {
                $errorMessage = 'Authentication failed. Please check your service account credentials.';
            }

            \Log::error('Google Calendar API error', [
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'calendar_id' => $calendarId
            ]);
                return redirect()->back()->withErrors(['error' => __('Google Calendar sync failed: :error', ['error' => $e->getMessage()])]);
        } catch (\Exception $e) {
            // Clear sync test status on failure
            Settings::update(SettingKey::IsGoogleCalendarSync->value, '0', $userId);

            \Log::error('Google Calendar sync failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
                return redirect()->back()->withErrors(['error' => __('Google Calendar sync failed: :error', ['error' => $e->getMessage()])]);
        }
    }

    /**
     * Update the Google Wallet settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGoogleWallet(Request $request)
    {
        try {
            $validated = $request->validate([
                'googleWalletIssuerId' => 'nullable|string|max:255',
                'googleWalletJson' => 'nullable|file|mimes:json|max:2048',
            ]);

            $settings = [
                'googleWalletIssuerId' => $validated['googleWalletIssuerId'] ?? '',
            ];

            // Handle JSON file upload
            if ($request->hasFile('googleWalletJson')) {
                $file = $request->file('googleWalletJson');
                $path = $file->store('google-wallet', 'public');
                $settings['googleWalletJsonPath'] = $path;
            }

            foreach ($settings as $key => $value) {
                $keyEnum = SettingKey::match($key);
                Settings::update($keyEnum?->value ?? strtoupper(preg_replace('/([A-Z])/', '_$1', $key)), $value);
            }

            return redirect()->back()->with('success', __('Google Wallet settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Google Wallet settings: :error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Update the Slack settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSlack(Request $request)
    {
        try {
            $userId = createdBy();
            $availableTemplates = \App\Models\NotificationTemplate::where('type', 'slack')->pluck('name', 'id')->toArray();

            $rules = [
                'slack_enabled' => 'boolean',
                'slack_webhook_url' => 'nullable|url'
            ];

            foreach ($availableTemplates as $templateId => $templateName) {
                $rules[$templateName] = 'boolean';
            }

            $validated = $request->validate($rules);

            // Update Slack configuration
            Settings::update('slack_enabled', $validated['slack_enabled'] ? '1' : '0', $userId);
            Settings::update('slack_webhook_url', $validated['slack_webhook_url'] ?? '', $userId);

            // Update notification settings
            $tenantId = \Illuminate\Support\Facades\Auth::user()?->tenant_id;
            if ($tenantId) {
                foreach ($availableTemplates as $templateId => $templateName) {
                    if (isset($validated[$templateName])) {
                        TenantNotificationTemplate::updateOrCreate(
                            [
                                'tenant_id' => $tenantId,
                                'template_id' => $templateId,
                                'type' => 'slack'
                            ],
                            ['is_active' => $validated[$templateName]]
                        );
                    }
                }
            }

            return redirect()->back()->with('success', __('Slack settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Slack settings: :error', ['error' => $e->getMessage()]));
        }
    }

    // public function getSlackNotifications()
    // {
    //     $userId = createdBy();
    //     $templates = \App\Models\NotificationTemplate::select('id', 'name')->get();
    //     $settings = [];

    //     foreach ($templates as $template) {
    //         $tenantTemplate = \App\Models\TenantNotificationTemplate::where('tenant_id', $tenantId)
    //             ->where('template_id', $template->id)
    //             ->where('type', 'slack')
    //             ->first();

    //         $settings[$template->name] = $userTemplate ? (bool) $userTemplate->is_active : false;
    //     }

    //     return response()->json($settings);
    // }

    public function getAvailableSlackNotifications()
    {
        $templates = \App\Models\NotificationTemplate::where('type', 'slack')->select(['id', 'name'])->get();
        $notifications = [];

        foreach ($templates as $template) {
            $notifications[] = [
                'name' => $template->name,
                'label' => $template->name
            ];
        }

        return response()->json($notifications);
    }

    public function getSlackConfig()
    {
        $userId = createdBy();
        return response()->json([
            'slack_enabled' => getSetting('slack_enabled', '0', $userId) === '1',
            'slack_webhook_url' => getSetting('slack_webhook_url', '', $userId)
        ]);
    }

    public function updateSlackNotifications(Request $request)
    {
        try {
            $userId = createdBy();
            $availableTemplates = \App\Models\NotificationTemplate::where('type', 'slack')->pluck('name', 'id')->toArray();

            $rules = [
                'slack_enabled' => 'boolean',
                'slack_webhook_url' => 'nullable|url'
            ];

            foreach ($availableTemplates as $templateId => $templateName) {
                $rules[$templateName] = 'boolean';
            }

            $validated = $request->validate($rules);

            // Update Slack configuration
            Settings::update('slack_enabled', $validated['slack_enabled'] ? '1' : '0', $userId);
            Settings::update('slack_webhook_url', $validated['slack_webhook_url'] ?? '', $userId);

            // Update notification settings
            $tenantId = \Illuminate\Support\Facades\Auth::user()?->tenant_id;
            if ($tenantId) {
                foreach ($availableTemplates as $templateId => $templateName) {
                    if (isset($validated[$templateName])) {
                        TenantNotificationTemplate::updateOrCreate(
                            [
                                'tenant_id' => $tenantId,
                                'template_id' => $templateId,
                                'type' => 'slack'
                            ],
                            ['is_active' => $validated[$templateName]]
                        );
                    }
                }
            }

            return redirect()->back()->with('success', __('Slack settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Slack settings: :error', ['error' => $e->getMessage()]));
        }
    }

     public function getSlackNotifications()
    {
        $tenantId = tenant('id');
        if (!$tenantId) {
            return response()->json([]);
        }
        $templates = \App\Models\NotificationTemplate::where('type', 'slack')->select(['id', 'name'])->get();
        $settings = [];

        foreach ($templates as $template) {
            $tenantTemplate = TenantNotificationTemplate::where('tenant_id', $tenantId)
                ->where('template_id', $template->id)
                ->where('type', 'slack')
            ->first();

            $settings[$template->name] = $tenantTemplate ? $tenantTemplate->is_active : false;
        }

        return response()->json($settings);
    }
    public function getAvailableTwilioNotifications()
    {
        $templates = \App\Models\NotificationTemplate::where('type', 'twilio')->select(['id', 'name'])->get();
        $notifications = [];

        foreach ($templates as $template) {
            $notifications[] = [
                'name' => $template->name,
                'label' => $template->name
            ];
        }

        return response()->json($notifications);
    }
    public function getTwilioNotifications()
    {
        $tenantId = tenant('id');
        if (!$tenantId) {
            return response()->json([]);
        }
        $templates = \App\Models\NotificationTemplate::where('type', 'twilio')->select(['id', 'name'])->get();
        $settings = [];

        foreach ($templates as $template) {
            $tenantTemplate = TenantNotificationTemplate::where('tenant_id', $tenantId)
                ->where('template_id', $template->id)
                ->where('type', 'twilio')
                ->first();

            $settings[$template->name] = $tenantTemplate ? $tenantTemplate->is_active : false;
        }

        return response()->json($settings);
    }
    public function getTwilioConfig()
    {
        return response()->json([
            'twilio_sid' => getSetting('twilio_sid', ''),
            'twilio_token' => getSetting('twilio_token', ''),
            'twilio_from' => getSetting('twilio_from', '')
        ]);
    }
public function updateTwilioNotifications(Request $request)
    {
        try {
            $userId = createdBy();
            $availableTemplates = \App\Models\NotificationTemplate::where('type', 'twilio')->pluck('name', 'id')->toArray();

            $rules = [
                'twilio_sid' => 'nullable|string',
                'twilio_token' => 'nullable|string',
                'twilio_from' => 'nullable|string'
            ];

            foreach ($availableTemplates as $templateId => $templateName) {
                $rules[$templateName] = 'boolean';
            }

            $validated = $request->validate($rules);

            // Update Twilio configuration
            Settings::update('twilio_sid', $validated['twilio_sid'] ?? '');
            Settings::update('twilio_token', $validated['twilio_token'] ?? '');
            Settings::update('twilio_from', $validated['twilio_from'] ?? '');

            // Update notification settings in tenant_notification_templates
            $tenantId = \Illuminate\Support\Facades\Auth::user()?->tenant_id;
            if ($tenantId) {
                foreach ($availableTemplates as $templateId => $templateName) {
                    if (isset($validated[$templateName])) {
                        TenantNotificationTemplate::updateOrCreate(
                            [
                                'tenant_id' => $tenantId,
                                'template_id' => $templateId,
                                'type' => 'twilio'
                            ],
                            ['is_active' => $validated[$templateName]]
                        );
                    }
                }
            }

            return redirect()->back()->with('success', __('Twilio settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update Twilio settings: :error', ['error' => $e->getMessage()]));
        }
    }

    public function testTwilioSMS(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        try {
            $twilioService = app(\App\Services\TwilioService::class);
            $result = $twilioService->sendTestMessage($request->phone, createdBy());

            if ($result) {
                return redirect()->back()->with('success', __('Test SMS sent successfully to :phone!', ['phone' => $request->phone]));
            }

            return redirect()->back()->with('error', __('Failed to send test SMS.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to send test SMS: :message', ['message' => $e->getMessage()]));
        }
    }

    public function testSlackWebhook(Request $request)
    {
        $request->validate([
            'webhook_url' => 'required|url'
        ]);

        try {
            $slackService = app(\App\Services\SlackService::class);
            $result = $slackService->sendTestMessage($request->webhook_url);

            if ($result) {
                return redirect()->back()->with('success', __('Test message sent successfully to Slack!'));
            }

            return redirect()->back()->with('error', __('Failed to send test message.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to send test message: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Clear application cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            \Artisan::call('optimize:clear');

            return redirect()->back()->with('success', __('Cache cleared successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to clear cache: :error', ['error' => $e->getMessage()]));
        }
    }
}
