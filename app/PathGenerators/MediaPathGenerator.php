<?php

namespace App\PathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    /**
     * Resolve tenant ID for path prefix so each tenant's media is stored under tenantID/media/media-id
     * (works for both disk and S3).
     */
    private function getTenantIdForPath(Media $media): ?string
    {
        if ($media->tenant_id !== null) {
            return (string) $media->tenant_id;
        }
        if (function_exists('createdBy') && createdBy() !== null) {
            return (string) createdBy();
        }
        if (function_exists('tenant') && tenant() !== null) {
            return (string) tenant()->getTenantKey();
        }
        return null;
    }

    /**
     * Base path segment: tenantID/media/{id} or fallback media/{model_id} when no tenant context.
     */
    private function getBasePath(Media $media, string $subPath = ''): string
    {
        $tenantId = $this->getTenantIdForPath($media);
        $mediaId = $media->id ?? $media->model_id;

        if ($tenantId !== null && $tenantId !== '') {
            return trim($tenantId . '/media/' . $mediaId . '/' . $subPath, '/') . '/';
        }

        return 'media/' . $mediaId . '/' . $subPath;
    }

    public function getPath(Media $media): string
    {
        return $this->getBasePath($media, '');
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media, 'conversions/');
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media, 'responsive-images/');
    }
}