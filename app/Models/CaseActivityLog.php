<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseActivityLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'case_id',
        'tenant_id',
        'created_by',
        'occurred_at',
        'source',
        'category',
        'event_key',
        'title',
        'description',
        'meta',
        'case_timeline_id',
        'subject_type',
        'subject_id',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'meta' => 'array',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function caseTimeline(): BelongsTo
    {
        return $this->belongsTo(CaseTimeline::class, 'case_timeline_id');
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
