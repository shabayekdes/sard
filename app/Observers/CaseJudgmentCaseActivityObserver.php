<?php

namespace App\Observers;

use App\Models\CaseJudgment;
use App\Services\CaseActivityLogger;

class CaseJudgmentCaseActivityObserver
{
    public function created(CaseJudgment $judgment): void
    {
        CaseActivityLogger::judgmentCreated($judgment);
    }

    public function updated(CaseJudgment $judgment): void
    {
        CaseActivityLogger::judgmentUpdated($judgment);
    }

    public function deleting(CaseJudgment $judgment): void
    {
        CaseActivityLogger::judgmentDeleted($judgment);
    }
}
