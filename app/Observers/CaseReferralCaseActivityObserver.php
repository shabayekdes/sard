<?php

namespace App\Observers;

use App\Models\CaseReferral;
use App\Services\CaseActivityLogger;

class CaseReferralCaseActivityObserver
{
    public function created(CaseReferral $referral): void
    {
        CaseActivityLogger::referralCreated($referral);
    }

    public function updated(CaseReferral $referral): void
    {
        CaseActivityLogger::referralUpdated($referral);
    }

    public function deleted(CaseReferral $referral): void
    {
        CaseActivityLogger::referralDeleted($referral);
    }
}
