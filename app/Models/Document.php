<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Document extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'folder_id',
        'file_path',
        'status',
        'confidentiality',
        'tags',
        'tenant_id'
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function folder()
    {
        return $this->belongsTo(DocumentFolder::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function currentVersion()
    {
        return $this->hasOne(DocumentVersion::class)->where('is_current', true);
    }

    public function comments()
    {
        return $this->hasMany(DocumentComment::class);
    }

    public function permissions()
    {
        return $this->hasMany(DocumentPermission::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function scopeWithPermissionCheck($query)
    {
        return $this->applyPermissionScope($query, 'documents');
    }


}