<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class DocumentVersion extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'document_id',
        'version_number',
        'file_path',
        'changes_description',
        'is_current',
        'tenant_id'
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->whereHas('document', function ($q) {
    //         $q->where('created_by', createdBy());
    //     });
    // }
}