<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ExpenseCategory extends BaseModel
{
    use HasFactory, AutoApplyPermissionCheck, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'created_by',
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
                $builder->where(function ($q) use ($companyId) {
                    $q->where('created_by', $companyId)
                      ->orWhereNull('created_by');
                });
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}