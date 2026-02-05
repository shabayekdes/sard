<?php

namespace App\Listeners;

use App\Enum\EmailTemplateName;
use App\Events\NewCourtCreated;
use App\Services\EmailTemplateService;
use Exception;

class NewCourtListener
{
    public function handle(NewCourtCreated $event)
    {
         if(isEmailTemplateEnabled(EmailTemplateName::COURT_CREATED, createdBy())){

        try {


            // Check if New Court email template is active for current user
            $emailService = new EmailTemplateService();

            $court = $event->court;

            if (!$court) {
                return;
            }

            // Load related data
            $courtType = \App\Models\CourtType::find($court->court_type_id);
            $circleType = \App\Models\CircleType::find($court->circle_type_id);

            // For courts, we typically notify the admin user since courts don't have their own email
            $adminUser = auth()->user();

            if (!$adminUser || !$adminUser->email) {
                return;
            }

            $variables = [
                '{user_name}' => auth()->user()->name ?? 'System Administrator',
                '{name}' => $court->name ?? 'Court Name',
                '{type}' => $courtType ? $courtType->name : 'Court not assigned',
                '{phoneno}' => $court->phone ?? 'Not provided',
                '{email}' => $court->email ?? 'Not provided',
                '{circle_type}' => $circleType ? $circleType->name : 'Not specified',
                '{address}' => $court->address ?? 'Not provided',
                '{app_name}' => config('app.name', 'Legal Management System'),
            ];

            // Get language from currently logged-in user
            $userLanguage = auth()->user()->lang ?? 'en';

            $emailService->sendTemplateEmailWithLanguage(
                EmailTemplateName::COURT_CREATED,
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
