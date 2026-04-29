<?php

namespace App\Observers;

use App\Models\CaseNote;
use App\Services\CaseActivityLogger;

class CaseNoteCaseActivityObserver
{
    public function created(CaseNote $note): void
    {
        foreach ($this->caseIds($note) as $caseId) {
            CaseActivityLogger::noteCreatedForCase($note, $caseId);
        }
    }

    public function updated(CaseNote $note): void
    {
        foreach ($this->caseIds($note) as $caseId) {
            CaseActivityLogger::noteUpdatedForCase($note, $caseId);
        }
    }

    /**
     * @return list<int>
     */
    private function caseIds(CaseNote $note): array
    {
        $raw = $note->case_ids ?? [];
        $ids = [];
        foreach ($raw as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
