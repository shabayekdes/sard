<?php

namespace App\Listeners;

use App\Enum\EmailTemplateName;
use App\Events\NewCaseCreated;
use App\Models\User;
use App\Services\TwilioService;
use Exception;

class SendNewCaseTwilioNotification
{
    public function __construct(
        private TwilioService $twilioService
    )
    {
    }

    public function handle(NewCaseCreated $event): void
    {
        $case = $event->case;

        $userId = $case->tenant_id ?? auth()->id();
        $client = $case->client;
        $contact = $case->client->phone;


        if (!$userId) {
            return;
        }
        if (isNotificationTemplateEnabled(EmailTemplateName::COURT_CREATED, createdBy(), 'twilio')) {

            $variables = [
                '{case_number}' => $case->case_id ?? '-',
                '{case_type}' => $case->caseType->name ?? '-',
                '{created_by}' => $case->creator->name ?? '-',
            ];

            try {
                $createdByUser = User::find($userId);
                $userLanguage = $createdByUser->lang ?? 'en';


                // Send notification to client if they have a phone number
                if ($client && !empty($client->phone)) {
                    $this->twilioService->sendTemplateMessageToPhone(
                        templateName: EmailTemplateName::CASE_CREATED,
                        variables: $variables,
                        toPhone: $contact,
                        language: $userLanguage,
                        userId: $userId
                    );
                }
            } catch (Exception $e) {
                \Log::error('Failed to send New Case Twilio notification: ' . $e->getMessage());
            }
        }
    }
}
