<?php

namespace App\Http\Resources;

use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stageData = is_array($this->stage_data) ? $this->stage_data : [];
        $courtId = $stageData['court_id'] ?? null;
        $courtName = null;

        if ($courtId) {
            $courtName = Court::query()->where('id', $courtId)->value('name');
        }

        return [
            'id' => $this->id,
            'case_id' => $this->case_id,
            'stage' => $this->stage,
            'referral_date' => optional($this->referral_date)->format('Y-m-d'),
            'referral_date_is_hijri' => (bool) $this->referral_date_is_hijri,
            'reminder_enabled' => (bool) $this->reminder_enabled,
            'reminder_duration' => $this->reminder_duration,
            'notes' => $this->notes,
            'attachments' => $this->attachments ?? [],
            'stage_data' => $stageData,
            'stage_case_number' => $stageData['case_number']
                ?? $stageData['reconciliation_case_number']
                ?? $stageData['request_number']
                ?? null,
            'stage_court_name' => $courtName,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
