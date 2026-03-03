<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class Permission extends BaseSpatiePermission
{
    use HasTranslations;

    public array $translatable = ['label', 'description'];

    protected $fillable = [
        'module',
        'name',
        'label',
        'description',
        'guard_name'
    ];
}