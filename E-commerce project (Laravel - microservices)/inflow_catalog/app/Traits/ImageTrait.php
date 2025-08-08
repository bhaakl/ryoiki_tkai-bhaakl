<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait ImageTrait
{
    public function uploadFromUrl($imageUrl, $model)
    {
        $filename = time() . '-' . Str::random(10) . '.jpg';
        $path = "images/" . class_basename($model) . "/" . $model->id;

        $original = Image::read(file_get_contents($imageUrl));
        $image = $original->toJpeg();
        $preview = $original->cover(300, 300)->toJpeg();

        Storage::disk('public')->put($path . "/" . $filename, $image);
        Storage::disk('public')->put($path . "/preview-" . $filename, $preview);

        return [
            'original' => 'storage/' . $path . "/" . $filename,
            'preview' => 'storage/' . $path . "/preview-" . $filename,
        ];
    }

    public function uploadPhoto(UploadedFile $photo, $model, $collection = 'default', $disc = 'public')
    {
        try {
            try {
                list($width, $height) = getimagesize($photo);
            } catch (\Exception $exception) {
                $width = 0;
                $height = 0;
            }
            $media = $model->addMedia($photo)
                ->withCustomProperties([
                    'width' => $width,
                    'height' => $height
                ])
                ->toMediaCollection($collection, $disc);
        } catch (\Exception $exception) {
            Log::error('upload photo error', ['upload photo error' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]);

            return false;
        }

        return $media;
    }
}
