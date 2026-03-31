<?php

namespace App\Notifications\Tenant;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ContextBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;

class TenantRegisteredNotification extends Notification
{
    
    public function __construct(protected $tenant)
    {
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->text('A new company has been registered!')
            ->username('Sard SAAS')
            ->image('https://framerusercontent.com/images/8GV6LzJju4jTkXfQwE14n70BOg.png')
            ->headerBlock(':tada: New Registration: ' . $this->tenant->name . ' :rocket:')
            ->contextBlock(function (ContextBlock $block) {
                $block->text('Company ID #' . $this->tenant->id);
            })
            ->sectionBlock(function (SectionBlock $block) {
                $block->text('Company Details:');
                $block->field("*Phone Number:*\n" . $this->tenant->phone)->markdown();
                $block->field("*Email:*\n" . $this->tenant->email)->markdown();
            })
            ->dividerBlock()
            ->sectionBlock(function (SectionBlock $block) {
                $block->text('Please activate company on https://' . config('app.url'))->verbatim();
            });
    }
}
