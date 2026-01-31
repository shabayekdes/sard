<?php

namespace App\Listeners;

use App\EmailTemplateName;
use App\Events\InvoiceSent;
use App\Models\User;
use App\Services\TwilioService;
use Exception;

class SendInvoiceSentTwilioNotification
{
    public function __construct(
        private TwilioService $twilioService
    ) {
        //
    }

    public function handle(InvoiceSent $event): void
    {
        $invoice = $event->invoice;
        $contact =$invoice->client->phone;

        if (isNotificationTemplateEnabled(EmailTemplateName::INVOICE_SENT, createdBy(), 'twilio')) {
            $variables = [
                '{invoice_number}' => $invoice->invoice_number ?? '-',
                '{due_date}' => $invoice->due_date ?? '-',
            ];

            try {
                session()->forget('twilio_error');

                $twilioSid = getSetting('twilio_sid', '', createdBy());
                $twilioToken = getSetting('twilio_token', '', createdBy());
                $twilioFrom = getSetting('twilio_from', '', createdBy());

                if (filled($twilioSid) && filled($twilioToken) && filled($twilioFrom)) {
                    $createdByUser = User::find(createdBy());
                    $userLanguage = $createdByUser->lang ?? 'en';
                    $this->twilioService->sendTemplateMessageToPhone(
                        templateName: EmailTemplateName::INVOICE_SENT,
                        variables: $variables,
                        toPhone: $contact,
                        language: $userLanguage,
                        userId: createdBy()
                    );
                } else {
                    session()->flash('twilio_error', 'Twilio configuration is incomplete.');
                }
            } catch (Exception $e) {
                session()->flash('twilio_error', 'Failed to send invoice sent notification: ' . $e->getMessage());
            }
        }
    }
}
