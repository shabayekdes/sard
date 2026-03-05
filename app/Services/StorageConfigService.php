<?php

namespace App\Services;

use App\Facades\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StorageConfigService
{
    private static $config = null;

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
            'max:' . $maxSize
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
     * Get complete storage configuration (tenant-aware: uses current tenant's storage settings).
     */
    public static function getStorageConfig(): array
    {
        $tenantId = function_exists('tenant') && tenant() !== null ? tenant('id') : null;
        $cacheKey = 'storage_config' . ($tenantId ? '.' . $tenantId : '.central');
        return Cache::remember($cacheKey, 300, function () {
            return self::loadStorageConfig();
        });
    }

    /**
     * Clear storage configuration cache (optionally for a specific tenant).
     */
    public static function clearCache(?string $tenantId = null): void
    {
        if ($tenantId !== null) {
            Cache::forget('storage_config.' . $tenantId);
        } else {
            Cache::forget('storage_config.central');
            if (function_exists('tenant') && tenant() !== null) {
                Cache::forget('storage_config.' . tenant('id'));
            }
        }
    }

    /**
     * Load storage configuration from settings (tenant-aware via Settings facade).
     */
    private static function loadStorageConfig(): array
    {
        try {
            $settings = Settings::group(['storage'])->all();
            // Settings use uppercase keys (STORAGE_TYPE, etc.)
            $storageType = $settings['STORAGE_TYPE'] ?? 'public';
            $diskName = match (strtolower((string) $storageType)) {
                's3' => 's3',
                'wasabi' => 'wasabi',
                default => 'public'
            };

            return [
                'disk' => $diskName,
                'allowed_file_types' => $settings['STORAGE_FILE_TYPES'] ?? 'jpg,png,webp,gif',
                'max_file_size_mb' => (int)($settings['STORAGE_MAX_UPLOAD_SIZE'] ?? 2),
                's3' => [
                    'key' => $settings['AWS_ACCESS_KEY_ID'] ?? '',
                    'secret' => $settings['AWS_SECRET_ACCESS_KEY'] ?? '',
                    'bucket' => $settings['AWS_BUCKET'] ?? '',
                    'region' => $settings['AWS_DEFAULT_REGION'] ?? 'us-east-1',
                    'url' => $settings['AWS_URL'] ?? '',
                    'endpoint' => $settings['AWS_ENDPOINT'] ?? '',
                ],
                'wasabi' => [
                    'key' => $settings['WASABI_ACCESS_KEY'] ?? '',
                    'secret' => $settings['WASABI_SECRET_KEY'] ?? '',
                    'bucket' => $settings['WASABI_BUCKET'] ?? '',
                    'region' => $settings['WASABI_REGION'] ?? 'us-east-1',
                    'url' => $settings['WASABI_URL'] ?? '',
                    'root' => $settings['WASABI_ROOT'] ?? '',
                ]
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
            'wasabi' => []
        ];
    }
}