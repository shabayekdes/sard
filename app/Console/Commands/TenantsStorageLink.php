<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TenantsStorageLink extends Command
{
    protected $signature = 'tenants:storage-link';

    protected $description = 'Create storage symlinks for all tenants';

    public function handle(): int
    {
        if (windows_os()) {
            $this->warn('Symlinks for tenant storage are not created on Windows.');
            return 0;
        }

        $suffixBase = config('tenancy.filesystem.suffix_base', 'tenant');
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found.');
            return 0;
        }

        foreach ($tenants as $tenant) {
            $tenantSuffix = $suffixBase . $tenant->getTenantKey();
            $targetPath = base_path('storage' . DIRECTORY_SEPARATOR . $tenantSuffix . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public');
            $linkPath = public_path('storage' . DIRECTORY_SEPARATOR . $tenantSuffix);

            if (File::exists($linkPath)) {
                $this->line("Storage link exists for tenant: {$tenantSuffix}");
                continue;
            }

            File::ensureDirectoryExists(dirname($targetPath));
            if (!File::exists($targetPath)) {
                File::ensureDirectoryExists($targetPath);
            }

            $linkDir = dirname($linkPath);
            if (!File::isDirectory($linkDir)) {
                File::ensureDirectoryExists($linkDir);
            }

            if (symlink($targetPath, $linkPath)) {
                $this->info("Storage link created for tenant: {$tenantSuffix}");
            } else {
                $this->error("Failed to create storage link for tenant: {$tenantSuffix}");
            }
        }

        return 0;
    }
}
