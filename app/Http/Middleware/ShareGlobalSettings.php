<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ShareGlobalSettings
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Ensure storage link exists
        $this->ensureStorageLink();

        Inertia::share([
            'globalSettings' => function () {
                return Settings::all(); // Use our helper function
            }
        ]);

        return $next($request);
    }

    /**
     * Ensure storage symlink exists (central link or tenant-specific link).
     */
    private function ensureStorageLink(): void
    {
        try {
            if (function_exists('tenant') && tenant() !== null) {
                $this->ensureTenantStorageLink();
            } else {
                $this->ensureCentralStorageLink();
            }
        } catch (\Throwable $e) {
            // Silently fail if unable to create link
        }
    }

    /**
     * Ensure central app storage symlink exists (public/storage → storage/app/public).
     */
    private function ensureCentralStorageLink(): void
    {
        if (!File::exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }
    }

    /**
     * Ensure tenant storage symlink exists (public/storage/tenant{id} → tenant storage).
     */
    private function ensureTenantStorageLink(): void
    {
        $suffixBase = config('tenancy.filesystem.suffix_base', 'tenant');
        $tenantSuffix = $suffixBase . tenant()->getTenantKey();
        $linkPath = public_path('storage' . DIRECTORY_SEPARATOR . $tenantSuffix);
        $targetPath = storage_path('app/public');

        if (File::exists($linkPath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($targetPath));
        if (!File::exists($targetPath)) {
            File::ensureDirectoryExists($targetPath);
        }

        if (!windows_os()) {
            $linkDir = dirname($linkPath);
            if (!File::isDirectory($linkDir)) {
                File::ensureDirectoryExists($linkDir);
            }
            symlink($targetPath, $linkPath);
        }
    }
}
