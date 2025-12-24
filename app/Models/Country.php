<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'nationality_name'];

    protected $fillable = [
        'name',
        'nationality_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
