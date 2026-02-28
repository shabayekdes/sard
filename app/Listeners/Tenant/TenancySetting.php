<?php

namespace App\Listeners\Tenant;

use App\Facades\Settings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TenancySetting
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event) {

            $settings = Settings::group('mail')->all();

            config([
                'mail.mailers.smtp.host' => $settings['EMAIL_HOST'],
                'mail.mailers.smtp.port' => $settings['EMAIL_PORT'],
                'mail.mailers.smtp.username' => $settings['EMAIL_USERNAME'],
                'mail.mailers.smtp.password' => $settings['EMAIL_PASSWORD'],
                'mail.mailers.smtp.encryption' => $settings['EMAIL_ENCRYPTION'],
            ]);
        }
    }
}
