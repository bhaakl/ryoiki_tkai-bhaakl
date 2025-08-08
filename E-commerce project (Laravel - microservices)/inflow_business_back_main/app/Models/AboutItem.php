<?php

namespace App\Models;

use App\Enums\AboutTemplates;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $id
 * @property $type
 * @property $title
 * @property $text
 */
class AboutItem extends Model implements HasMedia
{
    use UsesTenantConnection, InteractsWithMedia;

    public $timestamps = false;

    protected $fillable = [
        'type',
        'title',
        'text'
    ];

    protected $casts = [
        'type' => AboutTemplates::class,
    ];

    public function registerMediaConversions(Media|\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, config('media-library.preview_width'), config('media-library.preview_height'))
            ->nonOptimized()
            ->nonQueued();
    }
}
