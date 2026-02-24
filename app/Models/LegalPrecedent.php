<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class LegalPrecedent extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'case_name',
        'citation',
        'jurisdiction',
        'summary',
        'category_id',
        'relevance_score',
        'decision_date',
        'court_level',
        'key_points',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'key_points' => 'array',
        'decision_date' => 'date',
        'relevance_score' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ResearchCategory::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}