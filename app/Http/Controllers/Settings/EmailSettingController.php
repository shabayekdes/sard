<?php

namespace App\Http\Controllers\Settings;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailSettingController extends Controller
{
    /**
     * Get email settings for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @deprecated
     */
    public function getEmailSettings()
    {
        $rawSettings = settings();

        $defaultHost = config('mail.mailers.smtp.host');
        $defaultPort = config('mail.mailers.smtp.port', 587);
        $defaultUsername = config('mail.mailers.smtp.username');
        $defaultPassword = config('mail.mailers.smtp.password');
        $defaultEncryption = config('mail.mailers.smtp.encryption', 'tls');

        $settings = [
            'provider' => $rawSettings['email_provider'] ?? 'smtp',
            'driver' => $rawSettings['email_driver'] ?? config('mail.default', 'smtp'),
            'host' => $rawSettings['email_host'] ?? '',
            'port' => $rawSettings['email_port'] ?? (string) $defaultPort,
            'username' => $rawSettings['email_username'] ?? '',
            'password' => $rawSettings['email_password'] ?? '',
            'encryption' => $rawSettings['email_encryption'] ?? $defaultEncryption,
            'fromAddress' => $rawSettings['email_from_address'] ?? config('mail.from.address'),
            'fromName' => $rawSettings['email_from_name'] ?? config('mail.from.name')
        ];

        $user = Auth::user();
        if (!$user || $user->type !== 'superadmin') {
            $usesDefaultCredentials = ($rawSettings['email_host'] ?? null) === $defaultHost
                && ($rawSettings['email_username'] ?? null) === $defaultUsername
                && ($rawSettings['email_password'] ?? null) === $defaultPassword;

            if ($usesDefaultCredentials) {
                $settings['host'] = '';
                $settings['username'] = '';
                $settings['password'] = '';
            }
        }

        return response()->json($settings);
    }

    /**
     * Update email settings for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateEmailSettings(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'provider' => 'required|string',
            'driver' => 'required|string',
            'host' => 'nullable|string',
            'port' => 'nullable|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string',
            'fromAddress' => 'required|email',
            'fromName' => 'required|string',
        ]);

        Settings::update('email_provider', $validated['provider']);
        Settings::update('email_driver', $validated['driver']);
        if (!empty($validated['host'])) {
            Settings::update('email_host', $validated['host']);
        }
        if (!empty($validated['port'])) {
            Settings::update('email_port', $validated['port']);
        }
        if (!empty($validated['username'])) {
            Settings::update('email_username', $validated['username']);
        }
        if (!empty($validated['password']) && $validated['password'] !== '••••••••••••') {
            Settings::update('email_password', $validated['password']);
        }
        if (!empty($validated['encryption'])) {
            Settings::update('email_encryption', $validated['encryption']);
        }
        Settings::update('email_from_address', $validated['fromAddress']);
        Settings::update('email_from_name', $validated['fromName']);

        return redirect()->back()->with('success', __('Email settings updated successfully'));
    }

    /**
     * Send a test email.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function sendTestEmail(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $settings = [
            'provider' => getSetting('email_provider', 'smtp'),
            'driver' => getSetting('email_driver', config('mail.default', 'smtp')),
            'host' => getSetting('email_host') ?: config('mail.mailers.smtp.host'),
            'port' => getSetting('email_port', config('mail.mailers.smtp.port', 587)),
            'username' => getSetting('email_username') ?: config('mail.mailers.smtp.username'),
            'encryption' => getSetting('email_encryption', config('mail.mailers.smtp.encryption', 'tls')),
            'fromAddress' => getSetting('email_from_address', config('mail.from.address')),
            'fromName' => getSetting('email_from_name', config('mail.from.name'))
        ];
        
        // Get the actual password (not masked)
        $password = getSetting('email_password') ?: config('mail.mailers.smtp.password');
        
        try {
            // Configure mail settings for this request only
            config([
                'mail.default' => $settings['driver'],
                'mail.mailers.smtp.host' => $settings['host'],
                'mail.mailers.smtp.port' => $settings['port'],
                'mail.mailers.smtp.encryption' => $settings['encryption'] === 'none' ? null : $settings['encryption'],
                'mail.mailers.smtp.username' => $settings['username'],
                'mail.mailers.smtp.password' => $password,
                'mail.from.address' => $settings['fromAddress'],
                'mail.from.name' => $settings['fromName'],
            ]);


            // dd(config('mail'));
            // Send test email
            Mail::to($request->email)->send(new TestMail());

            return redirect()->back()->with('success', __('Test email sent successfully to :email', ["email" =>  $request->email]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to send test email: :message' , ["message" => $e->getMessage()]));
        }
    }

    /**
     * Get a setting value for a user.
     *
     * @param  int  $userId
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */

}