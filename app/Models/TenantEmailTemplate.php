<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantEmailTemplate extends BaseModel
{
    protected $table = 'tenant_email_templates';

    protected $fillable = [
        'template_id',
        'tenant_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
