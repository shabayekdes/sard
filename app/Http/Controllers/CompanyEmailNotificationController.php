<?php

namespace App\Http\Controllers;

use App\Models\TenantEmailTemplate;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompanyEmailNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? tenant('id');
        if (!$tenantId) {
            $templates = [];
            if ($request->expectsJson()) {
                return response()->json(['templates' => $templates]);
            }
            return Inertia::render('settings/email-notification-settings', ['templates' => $templates]);
        }

        $emailTemplates = EmailTemplate::get();

        // Get tenant's notification settings
        $tenantSettings = TenantEmailTemplate::where('tenant_id', $tenantId)
            ->pluck('is_active', 'template_id')
            ->toArray();

        // Format templates with settings
        $templates = $emailTemplates->map(function ($template) use ($tenantSettings) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'is_active' => $tenantSettings[$template->id] ?? true,
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'from' => $template->from
                ]
            ];
        });

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'templates' => $templates
            ]);
        }

        return Inertia::render('settings/email-notification-settings', [
            'templates' => $templates
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id ?? tenant('id');
        if (!$tenantId) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tenant context required.'], 422);
            }
            return redirect()->back()->withErrors(['error' => __('Tenant context required.')]);
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
                    'is_active' => $setting['is_enabled'] ?? $setting['is_active'] ?? true
                ]
            );
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Email notification settings updated successfully']);
        }
        
        return redirect()->back()->with('success', 'Email notification settings updated successfully');
    }
}