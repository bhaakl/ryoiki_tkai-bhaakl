<?php

namespace App\Models;

use App\Enums\AuthTypes;
use App\Enums\LoyaltyTypes;
use App\States\AppState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStates\HasStates;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property AppState $state
 * @property AuthTypes $auth_type
 * @property $two_factor
 * @property $jivo
 * @property $splash_screen_text
 * @property $icon_name
 * @property $loyalty_type
 * @property $loyalty_uuid
 * @property $has_delivery
 * @property $has_pickup
 * @property $has_services
 * @property $has_market
 * @property $has_favorite
 * @property $primary
 * @property $secondary
 * @property $background_1
 * @property $background_2
 * @property $icon
 * @property $text
 * @property $gradient_1
 * @property $gradient_2
 */
class AppSetting extends Model implements HasMedia
{
    use HasFactory, UsesTenantConnection, HasStates, InteractsWithMedia;

    protected $fillable = [
        'state',
        'auth_type',
        'two_factor',
        'jivo',
        'splash_screen_text',
        'icon_name',
        'loyalty_type',
        'loyalty_uuid',
        'has_delivery',
        'has_pickup',
        'has_services',
        'has_market',
        'has_favorite',
        'primary',
        'secondary',
        'background_1',
        'background_2',
        'icon',
        'text',
        'gradient_1',
        'gradient_2',
    ];

    protected $casts = [
        'auth_type' => AuthTypes::class,
        'loyalty_type' => LoyaltyTypes::class,
        'state' => AppState::class,
    ];

    protected static function booted()
    {
        static::created(function (AppSetting $setting) {
            AndroidSetting::create([]);
            IosSetting::create([]);
        });
    }

    public function getIosSettingAttribute(): ?IosSetting
    {
        return IosSetting::first();
    }

    public function getAndroidSettingAttribute(): ?AndroidSetting
    {
        return AndroidSetting::first();
    }

    public function getColors()
    {
        return [
            '{{primary}}' => $this->getAttribute('primary'),
            '{{secondary}}' => $this->getAttribute('secondary'),
            '{{background_1}}' => $this->getAttribute('background_1'),
            '{{background_2}}' => $this->getAttribute('background_2'),
            '{{icon}}' => $this->getAttribute('icon'),
            '{{text}}' => $this->getAttribute('text'),
            '{{gradient_1}}' => $this->getAttribute('gradient_1'),
            '{{gradient_2}}' => $this->getAttribute('gradient_2'),
        ];
    }

    public function registerMediaConversions(SpatieMedia $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, config('media-library.preview_width'), config('media-library.preview_height'))
            ->nonOptimized()
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(Media::LOGO_SVG_COLLECTION)->singleFile();
        $this->addMediaCollection(Media::LOGO_PDF_COLLECTION)->singleFile();
        $this->addMediaCollection(Media::LOGO_PNG_COLLECTION)->singleFile();
    }

    public function getBonusEnabled(): bool
    {
        return $this->loyalty_type == LoyaltyTypes::BONUS && $this->loyalty_uuid;
    }
}
