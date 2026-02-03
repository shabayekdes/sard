<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(Lang::get('email_verification_subject'))
            ->greeting(Lang::get('email_verification_greeting'))
            ->line(Lang::get('email_verification_intro'))
            ->action(Lang::get('email_verification_action'), $verificationUrl)
            ->line(Lang::get('email_verification_outro', [
                'count' => Config::get('auth.verification.expire', 60),
            ]))
            ->line(Lang::get('email_verification_no_action'))
            ->salutation(new HtmlString(sprintf(
                '%s<br>%s',
                e(Lang::get('email_verification_salutation')),
                e(config('app.name'))
            )));
    }
}
