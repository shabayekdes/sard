<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class Role extends BaseSpatieRole
{
    use HasTranslations;

    public array $translatable = ['label', 'description'];

    protected $fillable = [
        'name',
        'label',
        'description',
        'is_active',
        'guard_name',
        'created_by'
    ];

    protected $appends = ['is_system_role'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this is a system role that shouldn't be deleted
     *
     * @return bool
     */
    public function getIsSystemRoleAttribute()
    {
        $systemRoles = ['superadmin', 'super-admin', 'company'];
        return in_array(strtolower($this->name), $systemRoles);
    }
}