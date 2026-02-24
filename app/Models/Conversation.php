<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Conversation extends BaseModel
{
    use BelongsToTenant, AutoApplyPermissionCheck;

    /** @var string Use tenant_id only (table has no company_id) */
    public static $tenantIdColumn = 'tenant_id';

    public static function bootBelongsToTenant(): void
    {
        // Use our own scope with tenant_id instead of trait's TenantScope (which uses shared static that may be company_id)
        static::addGlobalScope('conversation_tenant', function (Builder $builder) {
            if (! tenancy()->initialized) {
                return;
            }
            $builder->where($builder->getModel()->qualifyColumn('tenant_id'), tenant()->getTenantKey());
        });

        static::creating(function ($model) {
            if (! $model->getAttribute('tenant_id') && ! $model->relationLoaded('tenant')) {
                if (tenancy()->initialized) {
                    $model->setAttribute('tenant_id', tenant()->getTenantKey());
                    $model->setRelation('tenant', tenant());
                }
            }
        });
    }

    protected $fillable = [
        'title',
        'type',
        'participants',
        'case_id',
        'last_message_at',
        'status',
        'tenant_id'
    ];

    protected $casts = [
        'participants' => 'array',
        'last_message_at' => 'datetime'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(Message::class)->latest()->limit(1);
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'), 'tenant_id');
    }

    public function getParticipantUsersAttribute()
    {
        return User::whereIn('id', $this->participants ?? [])->get();
    }

    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('recipient_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}