<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseReferralExecutionRespondent extends Model
{
    protected $fillable = [
        'referral_id',
        'name',
        'national_id',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(CaseReferral::class, 'referral_id');
    }
}
