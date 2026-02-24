<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseTeamMember extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'case_id',
        'user_id',
        'assigned_date',
        'status',
        'tenant_id',
        'google_calendar_event_id'
    ];

    protected $casts = [
        'assigned_date' => 'date',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function scopeWithPermissionCheck($query)
    {
        return $query->whereHas('case', function ($q) {
            $q->where('tenant_id', createdBy());
        });
    }
}