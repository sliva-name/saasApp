<?php

namespace App\Helpers;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TenantPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    protected function getBasePath(Media $media): string
    {
        $tenantId = tenant()?->getTenantKey() ?? 'central';
        return "tenants/{$tenantId}";
    }
}
