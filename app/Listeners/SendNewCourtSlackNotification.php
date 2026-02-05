<?php

namespace App\Listeners;

use App\Enum\EmailTemplateName;
use App\Events\NewCourtCreated;
use App\Models\User;
use App\Services\SlackService;
use Exception;

class SendNewCourtSlackNotification
{

    public function __construct(
        private SlackService $slackService
    ) {
        //
    }

    public function handle(NewCourtCreated $event): void
    {
        $court = $event->court;

       if (isNotificationTemplateEnabled(EmailTemplateName::COURT_CREATED, createdBy(), 'slack')) {
            // Load circle type relationship if not already loaded
            if (!$court->relationLoaded('circleType')) {
                $court->load('circleType');
            }
            
            $circleTypeName = '-';
            if ($court->circleType) {
                $circleTypeName = $court->circleType->name ?? '-';
            }
            
            $variables = [
                '{court_name}' => $court->name ?? '-',
                '{court_type}' => $court->courtType->name ?? '-',
                '{circle_type}' => $circleTypeName,
                '{location}' => $court->address ?? '-',
                '{created_by}' => $court->creator->name ?? '-',
            ];

            try {
                // Clear any existing slack error
                session()->forget('slack_error');

                $slackWebhookUrl = getSetting('slack_webhook_url', '', createdBy());

                if (filled($slackWebhookUrl)) {
                    $createdByUser = User::find(createdBy());
                    $userLanguage = $createdByUser->lang ?? 'en';
                    $this->slackService->sendTemplateMessageWithLanguage(
                        templateName: EmailTemplateName::COURT_CREATED,
                        variables: $variables,
                        webhookUrl: $slackWebhookUrl,
                        language: $userLanguage
                    );
                } else {
                    session()->flash('slack_error', 'Slack webhook URL is not set.');
                }
            } catch (Exception $e) {
                // Store error in session for frontend notification
                session()->flash('slack_error', 'Failed to send court Create notification: ' . $e->getMessage());
            }
        }
    }
}
