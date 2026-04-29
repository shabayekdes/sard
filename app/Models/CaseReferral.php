<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseReferral extends BaseModel
{
    use AutoApplyPermissionCheck, BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'case_id',
        'tenant_id',
        'stage',
        'referral_date',
        'referral_date_is_hijri',
        'reminder_enabled',
        'reminder_duration',
        'stage_data',
        'attachments',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'referral_date' => 'date',
        'referral_date_is_hijri' => 'boolean',
        'reminder_enabled' => 'boolean',
        'reminder_duration' => 'integer',
        'stage_data' => 'array',
        'attachments' => 'array',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function executionRequesters(): HasMany
    {
        return $this->hasMany(CaseReferralExecutionRequester::class, 'referral_id');
    }

    public function executionRespondents(): HasMany
    {
        return $this->hasMany(CaseReferralExecutionRespondent::class, 'referral_id');
    }
}
