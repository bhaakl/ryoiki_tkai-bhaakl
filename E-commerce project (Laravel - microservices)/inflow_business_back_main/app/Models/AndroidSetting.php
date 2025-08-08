<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $title
 * @property $short_description
 * @property $description
 * @property $user_agreement_url
 * @property $user_delete_form_url
 * @property $default_language
 * @property $app_category
 */
class AndroidSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, UsesTenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'user_agreement_url',
        'user_delete_form_url',
        'default_language',
        'app_category',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(Media::ANDROID_APP_ICON_COLLECTION);
        $this->addMediaCollection(Media::ANDROID_MARKET_BANNER_COLLECTION);
        $this->addMediaCollection(Media::ANDROID_MARKET_IMAGE_COLLECTION);
    }

    public function registerMediaConversions(SpatieMedia $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, config('media-library.preview_width'), config('media-library.preview_height'))
            ->nonOptimized()
            ->nonQueued();
    }
}
