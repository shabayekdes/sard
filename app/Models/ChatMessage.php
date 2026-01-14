<?php

namespace App\Models;

use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends BaseModel
{
    use HasFactory, AutoApplyPermissionCheck;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Message roles
     */
    const ROLE_USER = 'user';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_SYSTEM = 'system';

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Check if message is from user
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if message is from assistant
     */
    public function isAssistant(): bool
    {
        return $this->role === self::ROLE_ASSISTANT;
    }

    /**
     * Scope to get only user messages
     */
    public function scopeUserMessages($query)
    {
        return $query->where('role', self::ROLE_USER);
    }

    /**
     * Scope to get only assistant messages
     */
    public function scopeAssistantMessages($query)
    {
        return $query->where('role', self::ROLE_ASSISTANT);
    }
}

