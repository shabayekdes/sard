<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantNotificationTemplate extends Model
{
    protected $table = 'tenant_notification_templates';

    protected $fillable = [
        'template_id',
        'tenant_id',
        'status',
        'type',
    ];

    public function notificationTemplate(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function getTenantNotificationTemplateSettings($tenantId)
    {
        return self::where('tenant_id', $tenantId)
            ->with('notificationTemplate')
            ->get()
            ->pluck('status', 'notificationTemplate.name')
            ->map(fn ($status) => $status === 'active')
            ->toArray();
    }

    public static function isNotificationActive($templateName, $tenantId, $type = 'email')
    {
        $template = NotificationTemplate::where('name', $templateName)->first();
        if (!$template) {
            return false;
        }

        return self::where('tenant_id', $tenantId)
            ->where('template_id', $template->id)
            ->where('type', $type)
            ->where('status', 'active')
            ->exists();
    }

    public static function setNotificationStatus($templateName, $tenantId, $type, $isActive)
    {
        $template = NotificationTemplate::where('name', $templateName)->first();
        if (!$template) {
            return false;
        }

        return self::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'template_id' => $template->id,
                'type' => $type
            ],
            ['status' => $isActive ? 'active' : 'inactive']
        );
    }
}
