<?php

namespace App\Listeners;

use App\Enum\EmailTemplateName;
use App\Events\NewRegulatoryBodyCreated;
use App\Services\EmailTemplateService;
use Exception;

class NewRegulatoryBodyListener
{
    public function handle(NewRegulatoryBodyCreated $event)
    {
         if(isEmailTemplateEnabled(EmailTemplateName::REGULATORY_BODY_CREATED, createdBy())){

        try {


            $emailService = new EmailTemplateService();

            $regulatoryBody = $event->regulatoryBody;

            if (!$regulatoryBody) {
                return;
            }

            // For regulatory bodies, we typically notify the admin user
            $adminUser = auth()->user();

            if (!$adminUser || !$adminUser->email) {
                return;
            }

            $variables = [
                '{user_name}' => auth()->user()->name ?? 'System Administrator',
                '{name}' => $regulatoryBody->name ?? 'Regulatory Body',
                '{jurisdiction}' => $regulatoryBody->jurisdiction ?? 'Not specified',
                '{email}' => $regulatoryBody->contact_email ?? 'Not provided',
                '{phoneno}' => $regulatoryBody->contact_phone ?? 'Not provided',
                '{address}' => $regulatoryBody->address ?? 'Not provided',
                '{website}' => $regulatoryBody->website ?? 'Not provided',
                '{app_name}' => config('app.name', 'Legal Management System'),
            ];

            // Get language from currently logged-in user
            $userLanguage = auth()->user()->lang ?? 'en';

            $emailService->sendTemplateEmailWithLanguage(
                EmailTemplateName::REGULATORY_BODY_CREATED,
                $variables,
                (string) $adminUser->email,
                (string) $adminUser->name,
                $userLanguage
            );
        } catch (Exception $e) {
            return back()->withErrors(['error' => __($e->getMessage())]);
        }
    }
}
}
