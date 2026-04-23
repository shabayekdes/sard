<?php

namespace App\Listeners\Tenant;

use App\Facades\Settings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;

class TenancySetting
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event) {

            $settings = Settings::group(['mail', 'storage'])->all();

            config([
                'mail.mailers.smtp.host' => $settings['EMAIL_HOST'],
                'mail.mailers.smtp.port' => $settings['EMAIL_PORT'],
                'mail.mailers.smtp.username' => $settings['EMAIL_USERNAME'],
                'mail.mailers.smtp.password' => $settings['EMAIL_PASSWORD'],
                'mail.mailers.smtp.encryption' => $settings['EMAIL_ENCRYPTION'],
            ]);

            $suffixBase = config('tenancy.filesystem.suffix_base', 'tenant');
            $tenantSuffix = $suffixBase . '/' . tenant()->getTenantKey();
            $publicDiskUrl = rtrim(config('app.url'), '/') . '/storage/' . $tenantSuffix;

            config([
                'filesystems.disks.public' => array_merge(
                    config('filesystems.disks.public', []),
                    ['url' => $publicDiskUrl]
                ),
                // 'filesystems.disks.s3' => [
                //     'driver' => 's3',
                //     'key' => $settings['AWS_ACCESS_KEY_ID'] ?? null,
                //     'secret' => $settings['AWS_SECRET_ACCESS_KEY'] ?? null,
                //     'region' => $settings['AWS_DEFAULT_REGION'] ?? null,
                //     'bucket' => $settings['AWS_BUCKET'] ?? null,
                //     'url' => $settings['AWS_URL'] ?? null,
                //     'endpoint' => $settings['AWS_ENDPOINT'] ?? null,
                //     'use_path_style_endpoint' => !empty($settings['AWS_ENDPOINT']),
                //     'visibility' => 'public',
                // ],
                'filesystems.disks.wasabi' => [
                    'driver' => 's3',
                    'key' => $settings['WASABI_ACCESS_KEY'] ?? null,
                    'secret' => $settings['WASABI_SECRET_KEY'] ?? null,
                    'region' => $settings['WASABI_REGION'] ?? null,
                    'bucket' => $settings['WASABI_BUCKET'] ?? null,
                    'endpoint' => !empty($settings['WASABI_REGION']) ? 'https://s3.' . $settings['WASABI_REGION'] . '.wasabisys.com' : null,
                    'use_path_style_endpoint' => false,
                    'visibility' => 'public',
                ],
                // 'filesystems.disks.gcs' => [
                //     'driver' => 'gcs',
                //     'key_file_path' => $settings['GOOGLE_CLOUD_KEY_FILE'] ?? null,
                //     'project_id' => $settings['GOOGLE_CLOUD_PROJECT_ID'] ?? null,
                //     'bucket' => $settings['GOOGLE_CLOUD_STORAGE_BUCKET'] ?? null,
                //     'path_prefix' => $tenantSuffix, // Stancl tenancy uses this for tenant-specific paths
                //     'url' => $settings['GOOGLE_CLOUD_URL'] ?? null,
                //     'visibility' => 'public',
                // ]
            ]);

            // Ensure tenant public storage directory exists so uploads (e.g. media/3/) can create subdirs (0775 for live web server)
            $publicRoot = storage_path('app/public');
            if (!File::isDirectory($publicRoot)) {
                File::ensureDirectoryExists($publicRoot, 0775);
            }

            // Ensure tenant storage symlink exists (public/storage/tenant{id} → tenant storage) so URLs work
            $this->ensureTenantStorageLink($tenantSuffix, $publicRoot);
        }
    }

    /**
     * Create symlink public/storage/tenant{id} → tenant storage so tenant media URLs are publicly accessible.
     */
    private function ensureTenantStorageLink(string $tenantSuffix, string $targetPath): void
    {
        if (windows_os()) {
            return;
        }

        $linkPath = public_path('storage' . DIRECTORY_SEPARATOR . $tenantSuffix);
        if (File::exists($linkPath)) {
            return;
        }

        $linkDir = dirname($linkPath);
        if (!File::isDirectory($linkDir)) {
            File::ensureDirectoryExists($linkDir);
        }

        @symlink($targetPath, $linkPath);
    }
}
