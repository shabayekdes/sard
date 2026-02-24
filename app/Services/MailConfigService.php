<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class MailConfigService
{
    public static function setDynamicConfig()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $getSettings = settings($user->tenant_id);

        $settings = [
            'driver' => $getSettings['email_driver'] ?? config('mail.default', 'smtp'),
            'host' => $getSettings['email_host'] ?? config('mail.mailers.smtp.host'),
            'port' => $getSettings['email_port'] ?? config('mail.mailers.smtp.port'),
            'username' => $getSettings['email_username'] ?? config('mail.mailers.smtp.username'),
            'password' => $getSettings['email_password'] ?? config('mail.mailers.smtp.password'),
            'encryption' => $getSettings['email_encryption'] ?? config('mail.mailers.smtp.encryption', 'tls'),
            'fromAddress' => $getSettings['email_from_address'] ?? config('mail.from.address'),
            'fromName' => $getSettings['email_from_name'] ?? config('mail.from.name')
        ];

        Config::set([
            'mail.default' => $settings['driver'],
            'mail.mailers.smtp.host' => $settings['host'],
            'mail.mailers.smtp.port' => $settings['port'],
            'mail.mailers.smtp.encryption' => $settings['encryption'] === 'none' ? null : $settings['encryption'],
            'mail.mailers.smtp.username' => $settings['username'],
            'mail.mailers.smtp.password' => $settings['password'],
            'mail.from.address' => $settings['fromAddress'],
            'mail.from.name' => $settings['fromName'],
        ]);
    }
}