<?php

namespace App\Models;

use App\Enum\ResearchSourceType;
use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ResearchSource extends BaseModel
{
    use BelongsToTenant, HasFactory, HasTranslations, AutoApplyPermissionCheck;

    public array $translatable = ['source_name', 'description'];

    protected $fillable = [
        'source_name',
        'source_type',
        'description',
        'url',
        'access_info',
        'credentials',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'source_type' => ResearchSourceType::class,
        'credentials' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->where('created_by', createdBy());
    // }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}