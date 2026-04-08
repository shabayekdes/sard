<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaDownloadService
{
    /**
     * Resolve storage disk name from .env / config (same as media uploads).
     */
    public function getStorageDisk(): string
    {
        // Previously: Settings::string('STORAGE_TYPE')
        return StorageConfigService::getActiveDisk();
    }

    /**
     * Attempt to download a file by path or URL.
     * Handles: full URLs (http), /storage/ paths, and direct storage paths.
     *
     * @param  string  $filePath  Path or URL stored on the document (e.g. file_path).
     * @param  string  $downloadName  Filename to use for the download response.
     * @return Response|null  Download response, or null if file not found.
     */
    public function download(string $filePath, string $downloadName): ?Response
    {
        if (empty($filePath)) {
            return null;
        }

        // Handle full URLs (e.g. remote storage or media library URLs)
        if (str_starts_with($filePath, 'http')) {
            $response = $this->downloadFromUrl($filePath, $downloadName);
            if ($response !== null) {
                return $response;
            }
        }

        // Handle /storage/ paths (Laravel public storage)
        if (str_starts_with($filePath, '/storage/')) {
            $storagePath = str_replace('/storage/', '', $filePath);
            if (Storage::disk('public')->exists($storagePath)) {
                return response()->download(
                    storage_path('app/public/' . $storagePath),
                    $downloadName
                );
            }
        }

        // Try as direct storage path (relative to public disk root)
        if (Storage::disk('public')->exists($filePath)) {
            return response()->download(
                storage_path('app/public/' . $filePath),
                $downloadName
            );
        }

        return null;
    }

    /**
     * Try to resolve and download from a full URL (e.g. https://.../storage/...).
     * Uses configured storage disk (public, s3, wasabi, gcs) and path from URL.
     */
    protected function downloadFromUrl(string $url, string $downloadName): ?Response
    {
        $parsed = parse_url($url);
        if (! isset($parsed['path'])) {
            return null;
        }

        $path = $parsed['path'];
        $diskName = $this->getStorageDisk();
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($diskName);

        if ($disk->exists($path)) {
            return $disk->download($path, $downloadName);
        }

        // Path might be stored with leading slash; try without for some disks
        $pathTrimmed = ltrim($path, '/');
        if ($pathTrimmed !== $path && $disk->exists($pathTrimmed)) {
            return $disk->download($pathTrimmed, $downloadName);
        }

        // Fallback: try public path (e.g. demo media in public/storage/)
        $publicPath = public_path($pathTrimmed);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $downloadName);
        }

        return null;
    }
}
