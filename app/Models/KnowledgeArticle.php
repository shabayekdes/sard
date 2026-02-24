<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AutoApplyPermissionCheck;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class KnowledgeArticle extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'title',
        'content',
        'category_id',
        'tags',
        'is_public',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ResearchCategory::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }



    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}