<?php

namespace App\Services\Media;

use App\Models\Tenant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
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
        try {
            /** @var Tenant $tenant */
            $tenant = app('currentTenant');
            $prefix = $tenant->database;
        } catch (\Exception $e) {
            $prefix = config('media-library.prefix', 'media');
        }

        if ($prefix !== '') {
            return 'media/' . $prefix . '/' . class_basename($media->model) . '/' . $media->model->id;
        }

        return $media->getKey();
    }
}
