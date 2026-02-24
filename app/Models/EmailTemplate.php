<?php

namespace App\Models;

use App\Enum\EmailTemplateName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class EmailTemplate extends BaseModel
{
    use HasTranslations;

    public array $translatable = ['name', 'from', 'subject', 'content'];

    protected $fillable = [
        'name',
        'from',
        'subject',
        'content',
        'type',
        'user_id',
    ];

    protected $casts = [
        'type' => EmailTemplateName::class
    ];

    public function tenantEmailTemplates(): HasMany
    {
        return $this->hasMany(TenantEmailTemplate::class, 'template_id');
    }
}
