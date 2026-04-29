<?php

namespace App\Observers;

use App\Models\CaseDocument;
use App\Services\CaseActivityLogger;

class CaseDocumentCaseActivityObserver
{
    public function created(CaseDocument $document): void
    {
        CaseActivityLogger::documentCreated($document);
    }

    public function deleting(CaseDocument $document): void
    {
        CaseActivityLogger::documentDeleted($document);
    }
}
