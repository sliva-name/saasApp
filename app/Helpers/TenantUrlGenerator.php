<?php

namespace App\Helpers;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        // Здесь мы должны добавить '/storage' перед относительным путём
        $url = asset('storage/' . ltrim($this->getPathRelativeToRoot(), '/'));

        return $this->versionUrl($url);
    }
}
