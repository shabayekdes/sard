<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class CreateTenantStorageLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant
    ) {}

    public function handle(): void
    {
        if (windows_os()) {
            return;
        }

        $suffixBase = config('tenancy.filesystem.suffix_base', 'tenant');
        $tenantSuffix = $suffixBase . $this->tenant->getTenantKey();
        $targetPath = base_path('storage' . DIRECTORY_SEPARATOR . $tenantSuffix . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public');
        $linkPath = public_path('storage' . DIRECTORY_SEPARATOR . $tenantSuffix);

        if (File::exists($linkPath)) {
            return;
        }

        File::ensureDirectoryExists($targetPath);

        $linkDir = dirname($linkPath);
        if (!File::isDirectory($linkDir)) {
            File::ensureDirectoryExists($linkDir);
        }

        @symlink($targetPath, $linkPath);
    }
}
