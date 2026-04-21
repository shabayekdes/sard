<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Hearing extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'hearing_id',
        'case_id',
        'court_id',
        'circle_number',
        'judge_name',
        'hearing_type_id',
        'title',
        'description',
        'hearing_date',
        'hearing_time',
        'duration_minutes',
        'url',
        'status',
        'notes',
        'outcome',
        'attendees',
        'tenant_id',
        'google_calendar_event_id'
    ];

    protected $casts = [
        'attendees' => 'array',
        'hearing_date' => 'date',
        'hearing_time' => 'datetime:H:i',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($hearing) {
            if (!$hearing->hearing_id) {
                $hearing->hearing_id = 'HR' . str_pad(
                    (Hearing::max('id') ?? 0) + 1, 
                    6, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function hearingType()
    {
        return $this->belongsTo(HearingType::class);
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hearing_team_members')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }
}