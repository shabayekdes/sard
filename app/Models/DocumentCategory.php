<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class DocumentCategory extends BaseModel
{
    use BelongsToTenant, HasFactory, HasTranslations, AutoApplyPermissionCheck;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'color',
        'status',
        'tenant_id'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (!$category->category_id) {
                $category->category_id = 'DCAT' . str_pad(
                    (self::max('id') ?? 0) + 1, 
                    6, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    // public function scopeWithPermissionCheck($query)
    // {
    //     return $query->where('created_by', createdBy());
    // }
}