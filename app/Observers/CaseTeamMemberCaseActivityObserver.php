<?php

namespace App\Observers;

use App\Models\CaseTeamMember;
use App\Services\CaseActivityLogger;

class CaseTeamMemberCaseActivityObserver
{
    public function created(CaseTeamMember $member): void
    {
        CaseActivityLogger::assigneeAdded($member);
    }

    public function deleting(CaseTeamMember $member): void
    {
        CaseActivityLogger::assigneeRemoved($member);
    }
}
