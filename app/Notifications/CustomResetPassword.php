<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

class CustomResetPassword extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject(Lang::get('email_password_reset_subject'))
            ->greeting(Lang::get('email_password_reset_greeting'))
            ->line(Lang::get('email_password_reset_intro'))
            ->action(Lang::get('email_password_reset_action'), $resetUrl)
            ->line(Lang::get('email_password_reset_outro', [
                'count' => Config::get('auth.passwords.' . Config::get('auth.defaults.passwords') . '.expire'),
            ]))
            ->line(Lang::get('email_password_reset_no_action'))
            ->salutation(new HtmlString(sprintf(
                '%s<br>%s',
                e(Lang::get('email_verification_salutation')),
                e(config('app.name'))
            )));
    }
}
