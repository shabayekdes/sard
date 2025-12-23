<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Currency extends Model
{
    use HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'description',
        'is_default'
    ];
    
    protected $casts = [
        'is_default' => 'boolean'
    ];
}
