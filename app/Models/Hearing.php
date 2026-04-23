<?php

namespace App\Models;

use App\Services\StorageConfigService;
use App\Traits\AutoApplyPermissionCheck;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Hearing extends BaseModel implements HasMedia
{
    use BelongsToTenant, HasFactory, AutoApplyPermissionCheck, InteractsWithMedia;

    public const HEARING_FILES_COLLECTION = 'hearing_files';

    protected $fillable = [
        'hearing_id',
        'case_id',
        'court_id',
        'circle_number',
        'judge_name',
        'hearing_type_id',
        'title',
        'description',
        'hearing_date',
        'hearing_time',
        'duration_minutes',
        'url',
        'status',
        'notes',
        'outcome',
        'minutes_title',
        'minutes_date',
        'minutes_content',
        'attendees',
        'tenant_id',
        'google_calendar_event_id',
    ];

    protected $casts = [
        'attendees' => 'array',
        'hearing_date' => 'date',
        'hearing_time' => 'datetime:H:i',
        'minutes_date' => 'date',
    ];

    /**
     * Ordered list of Spatie `media.id` for this hearing's `hearing_files` collection
     * (replaces a JSON column; used by the hearing form and Inertia).
     */
    protected $appends = ['attachments'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hearing) {
            if (!$hearing->hearing_id) {
                $hearing->hearing_id = 'HR' . str_pad(
                    (Hearing::max('id') ?? 0) + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }

    public function getAttachmentsAttribute(): array
    {
        if (!$this->getKey()) {
            return [];
        }

        return $this->getMedia(self::HEARING_FILES_COLLECTION)
            ->sortBy('order_column')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    public function registerMediaCollections(): void
    {
        $config = StorageConfigService::getStorageConfig();
        $allowedExtensions = array_map('trim', explode(',', strtolower($config['allowed_file_types'])));
        $maxSizeBytes = ($config['max_file_size_mb'] ?? 2) * 1024 * 1024;
        $activeDisk = StorageConfigService::getActiveDisk();

        $this->addMediaCollection(self::HEARING_FILES_COLLECTION)
            ->acceptsFile(function ($file) use ($allowedExtensions, $maxSizeBytes) {
                $fileName = $file->name ?? $file->getFilename();
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions)) {
                    return false;
                }

                $fileSize = $file->size ?? filesize($file->getPathname());
                if ($fileSize > $maxSizeBytes) {
                    return false;
                }

                return true;
            })
            ->useDisk($activeDisk);
    }

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function hearingType()
    {
        return $this->belongsTo(HearingType::class);
    }

    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hearing_team_members')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'tenant_id', 'tenant_id')->where('type', 'company');
    }
}
