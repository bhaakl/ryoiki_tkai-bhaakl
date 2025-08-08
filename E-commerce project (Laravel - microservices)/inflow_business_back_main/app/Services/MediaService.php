<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Tenant;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(
        public AppSetting $app,
    ) {
    }

    public function saveFile($file, string $collection)
    {
        $uniqueName = $this->generateUniqueFileName($file);
        $media = $this->app->addMedia($file)
            ->usingFileName($uniqueName)
            ->toMediaCollection($collection);

        return $media->fresh();
    }

    private function generateUniqueFileName($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = Str::uuid()->toString();

        return $baseName . '.' . $extension;
    }
}
