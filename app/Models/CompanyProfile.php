<?php

namespace App\Models;

use App\Enums\CompanySize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CompanyProfile extends BaseModel
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'registration_number',
        'address',
        'phone',
        'email',
        'establishment_date',
        'company_size',
        'business_type',
        'cr',
        'tax_number',
        'office_hours',
        'consultation_fees',
        'success_rate',
        'services_offered',
        'default_setup',
        'description',
        'tenant_id',
    ];

    protected $casts = [
        'establishment_date' => 'date',
        'company_size' => CompanySize::class,
    ];

    /**
     * Get the user who created the company profile.
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    /**
     * Get the practice areas for this company.
     */
    public function practiceAreas()
    {
        return $this->hasMany(PracticeArea::class, 'company_profile_id');
    }
}