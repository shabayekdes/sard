<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TaskComment extends BaseModel
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck, SoftDeletes;

    protected $fillable = [
        'task_id',
        'comment_text',
        'is_internal',
        'tenant_id',
        'user_id'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }

    public function canBeUpdatedBy(User $user): bool
    {
        // Comment creator can always update
        if ($this->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function canBeDeletedBy(User $user): bool
    {
        // Comment creator can always delete
        if ($this->user_id === $user->id) {
            return true;
        }

        return false;
    }
}