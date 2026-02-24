<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ExpenseCategory extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected static function booted()
    {
        static::addGlobalScope('company', function ($builder) {
            if (auth()->check() && auth()->user()->type !== 'super admin') {
                $companyId = createdBy();
                $builder->where('tenant_id', $companyId);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}