<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NotificationTemplate extends Model
{
    use HasTranslations;

    public array $translatable = ['title', 'content'];

    protected $fillable = [
        'name',
        'type',
        'title',
        'content',
    ];
}