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
 * @property $user_agreement_url
 * @property $support_url
 * @property $description
 * @property $key_words
 * @property $name
 * @property $address
 * @property $email
 * @property $phone
 * @property $copyright
 */
class IosSetting extends Model implements HasMedia
{
    use HasFactory, UsesTenantConnection, InteractsWithMedia;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'user_agreement_url',
        'support_url',
        'description',
        'key_words',
        'name',
        'address',
        'email',
        'phone',
        'copyright',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(Media::IPHONE_5_5_8_COLLECTION);
        $this->addMediaCollection(Media::IPHONE_6_5_11_COLLECTION);
        $this->addMediaCollection(Media::IPHONE_6_7_17_COLLECTION);
        $this->addMediaCollection(Media::IPAD_PRO_3_COLLECTION);
        $this->addMediaCollection(Media::IOS_APP_ICON_COLLECTION)->singleFile();
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
