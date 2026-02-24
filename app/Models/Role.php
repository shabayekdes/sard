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
        'tenant_id'
    ];

    protected $appends = ['is_system_role'];

    /**
     * Get the company user for this tenant (for backward-compat creator display).
     */
    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
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