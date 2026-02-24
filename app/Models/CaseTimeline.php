<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseTimeline extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'case_id',
        'event_type_id',
        'title',
        'description',
        'event_date',
        'is_completed',
        'status',
        'tenant_id',
        'google_calendar_event_id',
        'meeting_link'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->whereHas('case', function ($q) {
    //         $q->where('created_by', createdBy());
    //     });
    // }
}