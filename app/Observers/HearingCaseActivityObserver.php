<?php

namespace App\Observers;

use App\Models\Hearing;
use App\Services\CaseActivityLogger;

class HearingCaseActivityObserver
{
    public function created(Hearing $hearing): void
    {
        CaseActivityLogger::hearingCreated($hearing);
    }

    public function updated(Hearing $hearing): void
    {
        CaseActivityLogger::hearingUpdated($hearing);
    }

    public function deleting(Hearing $hearing): void
    {
        CaseActivityLogger::hearingDeleted($hearing);
    }
}
