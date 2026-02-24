<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Client extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'tax_rate',
        'phone',
        'country_id',
        'address',
        'client_type_id',
        'status',
        'company_name',
        'tax_id',
        'date_of_birth',
        'notes',
        'tenant_id',
        'business_type',
        'nationality_id',
        'id_number',
        'gender',
        'unified_number',
        'cr_number',
        'cr_issuance_date',
        'vat_number',
        'country',
        'city',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'cr_issuance_date' => 'date',
        'tax_rate' => 'decimal:2',
    ];

    /**
     * Boot method to auto-generate client ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (!$client->client_id) {
                $client->client_id = 'CL' . str_pad(
                    (Client::max('id') ?? 0) + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    /**
     * Get the client type that owns the client.
     */
    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    /**
     * Get the company user for this tenant (for backward-compat creator display).
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseModel::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function billingInfo()
    {
        return $this->hasOne(ClientBillingInfo::class);
    }

    public function documents()
    {
        return $this->hasMany(ClientDocument::class);
    }

    /**
     * Get the nationality (country) that owns the client.
     */
    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }
}
