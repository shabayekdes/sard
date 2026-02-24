<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\TenantEmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailNotificationController extends Controller
{
    public function getNotificationSettings()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? tenant('id');
        if (!$tenantId) {
            return response()->json(['templates' => []]);
        }

        $emailTemplates = EmailTemplate::get();

        $tenantSettings = TenantEmailTemplate::where('tenant_id', $tenantId)
            ->get()
            ->keyBy('template_id');

        $templates = $emailTemplates->map(function (EmailTemplate $template) use ($tenantSettings, $tenantId) {
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
                    'tenant_id' => $tenantId,
                    'is_active' => 0
                ]);
            }

            // Switch ON only if is_active = 1
            $isEnabled = $tenantSetting->is_active == 1;

            return [
                'id' => $template->id,
                'name' => $name,
                'is_active' => $isEnabled,
                'template' => [
                    'id' => $template->id,
                    'name' => $name,
                    'from' => $from
                ]
            ];
        });

        return response()->json([
            'templates' => $templates
        ]);
    }

    public function updateNotificationSettings(Request $request)
    {
        try {
            $user = Auth::user();
            $tenantId = $user->tenant_id ?? tenant('id');
            if (!$tenantId) {
                return back()->withErrors(['error' => __('Tenant context required.')]);
            }
            $settings = $request->input('settings', []);

            foreach ($settings as $setting) {
                $template = EmailTemplate::find($setting['template_id'] ?? null);

                TenantEmailTemplate::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'template_id' => $setting['template_id']
                    ],
                    [
                        'is_active' => $setting['is_enabled'] ?? false
                    ]
                );
            }

            return back()->with('success', 'Email notification settings updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
