<?php

namespace App\Traits;

use App\Enums\ImageFormats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait ImageTrait
{
    public function uploadFromUrl($imageUrl, ImageFormats $val)
    {
        $filename = time() . '-' . Str::random(10) . $val->extention();
        $path = "images/" . class_basename($this) . "/" . $this->id;

        $original = Image::read(file_get_contents($imageUrl));
        $image = $original->{$val->method()}();
        $preview = $original->cover(300, 300)->toJpeg();

        Storage::disk('public')->put($path . "/" . $filename, $image);
        Storage::disk('public')->put($path . "/preview-" . $filename, $preview);

        return [
            'original' => 'storage/' . $path . "/" . $filename,
            'preview' => 'storage/' . $path . "/preview-" . $filename,
        ];
    }

    public function uploadPhoto(UploadedFile $photo, $model, $collection = 'default', $disc = 'public', $properties = [])
    {
        try {
            try {
                list($width, $height) = getimagesize($photo);
            } catch (\Exception $exception) {
                $width = 0;
                $height = 0;
            }
            $custom_properties = array_merge($properties, [
                'width' => $width,
                'height' => $height
            ]);
            $media = $model->addMedia($photo)
                ->withCustomProperties($custom_properties)
                ->toMediaCollection($collection, $disc);
        } catch (\Exception $exception) {
            Log::error('upload photo error', ['upload photo error' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]);

            return false;
        }

        return $media;
    }
}
