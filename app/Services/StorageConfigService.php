<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StorageConfigService
{
    /**
     * Get the active storage disk name (tenant-aware: uses current tenant's storage type).
     */
    public static function getActiveDisk(): string
    {
        $config = self::getStorageConfig();
        $disk = $config['disk'] ?? 'public';

        if ($disk === 's3' && !self::hasValidS3Config($config)) {
            return 'public';
        }

        if ($disk === 'wasabi' && !self::hasValidWasabiConfig($config)) {
            return 'public';
        }

        if ($disk === 'gcs' && !self::hasValidGcsConfig($config)) {
            return 'public';
        }

        return $disk;
    }

    /**
     * Get file validation rules based on settings
     */
    public static function getFileValidationRules(): array
    {
        $config = self::getStorageConfig();

        $allowedTypes = $config['allowed_file_types'] ?? '';
        $maxSize = ($config['max_file_size_mb'] ?? 2) * 1024; // Convert MB to KB

        // Map file extensions to MIME types
        $mimeTypes = self::getMimeTypesFromExtensions($allowedTypes);

        return [
            'mimes:' . $mimeTypes,
            'max:' . $maxSize,
        ];
    }

    /**
     * Convert file extensions to MIME types
     */
    private static function getMimeTypesFromExtensions($extensions): string
    {
        // Just return the extensions as Laravel mimes rule accepts extensions
        return $extensions;
    }

    /**
     * Get complete storage configuration from .env / config (not the settings table).
     */
    public static function getStorageConfig(): array
    {
        return Cache::remember('storage_config.env', 300, function () {
            return self::loadStorageConfig();
        });
    }

    /**
     * Clear storage configuration cache (optionally for a specific tenant — legacy no-op for tenant id).
     */
    public static function clearCache(?string $tenantId = null): void
    {
        Cache::forget('storage_config.env');
        // Legacy keys from when storage was read per-tenant from DB
        Cache::forget('storage_config.central');
        if ($tenantId !== null) {
            Cache::forget('storage_config.' . $tenantId);
        }
        if (function_exists('tenant') && tenant() !== null) {
            Cache::forget('storage_config.' . tenant('id'));
        }
        $user = Auth::user();
        if ($user && $user->tenant_id) {
            Cache::forget('storage_config.' . $user->tenant_id);
        }
    }

    /**
     * GCS slice for StorageConfigService from filesystems.disks.gcs (env-backed config).
     */
    private static function gcsConfigFromFilesystems(): array
    {
        $disk = config('filesystems.disks.gcs', []);

        return [
            'key_file_path' => (string) ($disk['key_file_path'] ?? ''),
            'project_id' => (string) ($disk['project_id'] ?? ''),
            'bucket' => (string) ($disk['bucket'] ?? ''),
            'path_prefix' => (string) ($disk['root'] ?? ''),
            'storage_api_uri' => (string) ($disk['storage_api_uri'] ?? ''),
            'api_endpoint' => (string) ($disk['api_endpoint'] ?? ''),
        ];
    }

    /**
     * Load storage configuration from .env / config/filesystems.php (not settings table).
     */
    private static function loadStorageConfig(): array
    {
        try {
            /*
            // Previously: tenant / central rows from `settings` (group storage)
            $settings = Settings::group(['storage'])->all();
            $storageType = $settings['STORAGE_TYPE'] ?? 'public';
            ...
            'gcs' => self::mergeGcsSettingsWithRuntimeDiskConfig($settings),
            */

            $defaultDisk = config('filesystems.default', env('FILESYSTEM_DISK', 'public'));
            $diskName = match (strtolower((string) $defaultDisk)) {
                's3' => 's3',
                'wasabi' => 'wasabi',
                'gcs' => 'gcs',
                default => 'public',
            };

            $s3Disk = config('filesystems.disks.s3', []);

            return [
                'disk' => $diskName,
                'allowed_file_types' => env(
                    'STORAGE_FILE_TYPES',
                    'jpg,png,webp,gif,pdf,doc,docx,txt,csv'
                ),
                'max_file_size_mb' => (int) env('STORAGE_MAX_UPLOAD_MB', '2'),
                's3' => [
                    'key' => (string) ($s3Disk['key'] ?? env('AWS_ACCESS_KEY_ID', '')),
                    'secret' => (string) ($s3Disk['secret'] ?? env('AWS_SECRET_ACCESS_KEY', '')),
                    'bucket' => (string) ($s3Disk['bucket'] ?? env('AWS_BUCKET', '')),
                    'region' => (string) ($s3Disk['region'] ?? env('AWS_DEFAULT_REGION', 'us-east-1')),
                    'url' => (string) ($s3Disk['url'] ?? env('AWS_URL', '')),
                    'endpoint' => (string) ($s3Disk['endpoint'] ?? env('AWS_ENDPOINT', '')),
                ],
                'wasabi' => [
                    'key' => env('WASABI_ACCESS_KEY', ''),
                    'secret' => env('WASABI_SECRET_KEY', ''),
                    'bucket' => env('WASABI_BUCKET', ''),
                    'region' => env('WASABI_REGION', 'us-east-1'),
                    'url' => env('WASABI_URL', ''),
                    'root' => env('WASABI_ROOT', ''),
                ],
                'gcs' => self::gcsConfigFromFilesystems(),
            ];
        } catch (\Throwable $e) {
            \Log::error('Failed to load storage config', ['error' => $e->getMessage()]);

            return self::getDefaultConfig();
        }
    }

    private static function hasValidS3Config(array $config): bool
    {
        $s3 = $config['s3'] ?? [];

        return !empty($s3['key'])
            && !empty($s3['secret'])
            && !empty($s3['bucket'])
            && !empty($s3['region']);
    }

    private static function hasValidWasabiConfig(array $config): bool
    {
        $wasabi = $config['wasabi'] ?? [];

        return !empty($wasabi['key'])
            && !empty($wasabi['secret'])
            && !empty($wasabi['bucket'])
            && !empty($wasabi['region']);
    }

    public static function hasValidGcsConfig(array $config): bool
    {
        $gcs = $config['gcs'] ?? [];
        if (empty($gcs['bucket'])) {
            return false;
        }

        return !empty($gcs['project_id']) || !empty($gcs['key_file_path']);
    }

    /**
     * Get default storage configuration
     */
    private static function getDefaultConfig(): array
    {
        return [
            'disk' => 'public',
            'allowed_file_types' => 'jpg,png,webp,gif',
            'max_file_size_mb' => 2,
            's3' => [],
            'wasabi' => [],
            'gcs' => [],
        ];
    }
}
