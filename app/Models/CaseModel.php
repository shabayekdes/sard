<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CaseModel extends BaseModel
{
    use AutoApplyPermissionCheck, BelongsToTenant, HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'case_id',
        'case_number',
        'file_number',
        'attributes',
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
        'case_status_id',
        'court_id',
        'authority_type',
        'authority_type_details',
        'priority',
        'filing_date',
        'expected_completion_date',
        'estimated_value',
        'opposing_party',
        'court_details',
        'status',
        'tenant_id',
        'google_calendar_event_id',
    ];

    protected $casts = [
        'filing_date' => 'date',
        'expected_completion_date' => 'date',
        'estimated_value' => 'decimal:2',
        'authority_type_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($case) {
            if (! $case->case_id) {
                $case->case_id = 'CASE'.str_pad(
                    (self::max('id') ?? 0) + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function caseType()
    {
        return $this->belongsTo(CaseType::class);
    }

    public function caseCategory()
    {
        return $this->belongsTo(CaseCategory::class, 'case_category_id');
    }

    public function caseSubcategory()
    {
        return $this->belongsTo(CaseCategory::class, 'case_subcategory_id');
    }

    public function caseStatus()
    {
        return $this->belongsTo(CaseStatus::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'case_id');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'case_id');
    }

    public function teamMembers()
    {
        return $this->hasMany(CaseTeamMember::class, 'case_id');
    }

    public function judgments()
    {
        return $this->hasMany(CaseJudgment::class, 'case_id');
    }

    public function oppositeParties()
    {
        return $this->hasMany(OppositeParty::class, 'case_id');
    }
}
