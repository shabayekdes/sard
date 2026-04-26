<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TaskAttachment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'task_id',
        'name',
        'file_path',
        'uploaded_by',
        'tenant_id',
    ];

    protected $appends = ['media_item'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getMediaItemAttribute(): ?array
    {
        if (! $this->file_path) {
            return null;
        }

        $url = $this->resolvePublicFileUrl();
        $mime = $this->guessMimeType();
        $thumb = str_starts_with($mime, 'image/') ? $url : $url;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $url,
            'thumb_url' => $thumb,
            'mime_type' => $mime,
        ];
    }

    protected function resolvePublicFileUrl(): string
    {
        $path = $this->file_path;
        if ($path && str_starts_with($path, 'http')) {
            return $path;
        }
        if (! $path) {
            return '';
        }
        if (! str_starts_with($path, '/')) {
            $path = '/'.$path;
        }

        return rtrim((string) config('app.url'), '/').$path;
    }

    protected function guessMimeType(): string
    {
        $path = (string) (parse_url((string) $this->file_path, PHP_URL_PATH) ?? $this->file_path);
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }
}
