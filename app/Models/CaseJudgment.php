<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseJudgment extends BaseModel
{
    use AutoApplyPermissionCheck, BelongsToTenant, HasFactory;

    protected $fillable = [
        'case_id',
        'tenant_id',
        'judgment_number',
        'judgment_date',
        'receipt_date',
        'appeal_deadline_date',
        'appeal_reminder_enabled',
        'appeal_reminder_duration',
        'appeal_reminder_custom_days',
        'status',
        'attachment_paths',
        'grounds',
        'summary',
    ];

    protected $casts = [
        'judgment_date' => 'date',
        'receipt_date' => 'date',
        'appeal_deadline_date' => 'date',
        'appeal_reminder_enabled' => 'boolean',
        'appeal_reminder_custom_days' => 'integer',
        'attachment_paths' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }
}
