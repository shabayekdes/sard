<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class OppositeParty extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'case_id',
        'name',
        'id_number',
        'nationality_id',
        'lawyer_name',
        'business_type',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'tenant_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function creator(): BelongsTo
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }
}
