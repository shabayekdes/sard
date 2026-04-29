<?php

namespace App\Observers;

use App\Models\CaseModel;
use App\Services\CaseActivityLogger;

class CaseModelCaseActivityObserver
{
    private const DATA_FIELDS = [
        'title',
        'description',
        'case_subject',
        'plaintiff_requests',
        'plaintiff_evidence',
        'defendant_requests',
        'defendant_evidence',
        'client_id',
        'case_type_id',
        'case_category_id',
        'case_subcategory_id',
        'court_id',
        'authority_type',
        'authority_type_details',
        'priority',
        'filing_date',
        'expected_completion_date',
        'estimated_value',
        'opposing_party',
        'court_details',
        'case_number',
        'file_number',
        'attributes',
    ];

    public function created(CaseModel $case): void
    {
        CaseActivityLogger::caseCreated($case);
    }

    public function updated(CaseModel $case): void
    {
        if ($case->wasChanged('status')) {
            CaseActivityLogger::caseActivationToggled($case, $case->status);

            return;
        }

        if ($case->wasChanged('case_status_id')) {
            CaseActivityLogger::caseWorkflowStatusChanged(
                $case,
                $case->getOriginal('case_status_id') !== null ? (int) $case->getOriginal('case_status_id') : null,
                $case->case_status_id !== null ? (int) $case->case_status_id : null
            );

            return;
        }

        foreach (self::DATA_FIELDS as $field) {
            if ($case->wasChanged($field)) {
                CaseActivityLogger::caseDataUpdated($case);

                return;
            }
        }
    }
}
