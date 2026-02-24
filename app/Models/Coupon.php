<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Coupon extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'type',
        'minimum_spend',
        'maximum_spend',
        'discount_amount',
        'use_limit_per_coupon',
        'use_limit_per_user',
        'expiry_date',
        'code',
        'code_type',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'minimum_spend' => 'decimal:2',
        'maximum_spend' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'expiry_date' => 'date',
        'status' => 'boolean'
    ];

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }
}
