<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property $ext_id
 * @property $name
 * @property $is_active
 */
class Media extends SpatieMedia
{
    use UsesTenantConnection;

    public const LOGO_SVG_COLLECTION = 'logo_svg';
    public const LOGO_PDF_COLLECTION = 'logo_pdf';
    public const LOGO_PNG_COLLECTION = 'logo_png';
    public const IOS_APP_ICON_COLLECTION = 'ios_app_icon';
    public const IPHONE_5_5_8_COLLECTION = 'iphone5_5-8';
    public const IPHONE_6_5_11_COLLECTION = 'iphone6_5-11';
    public const IPHONE_6_7_17_COLLECTION = 'iphone6_7-14';
    public const IPAD_PRO_3_COLLECTION = 'ipad_pro_3';
    public const ANDROID_APP_ICON_COLLECTION = 'android_app_icon';
    public const ANDROID_MARKET_BANNER_COLLECTION = 'market_banner';
    public const ANDROID_MARKET_IMAGE_COLLECTION = 'market_image';
    public const DEFAULT_COLLECTION = 'default';

    public const MAIN_COLLECTIONS = [
        self::LOGO_SVG_COLLECTION,
        self::LOGO_PDF_COLLECTION,
        self::LOGO_PNG_COLLECTION,
    ];

    public const COLLECTIONS = [
        self::LOGO_SVG_COLLECTION,
        self::LOGO_PDF_COLLECTION,
        self::LOGO_PNG_COLLECTION,
        self::IOS_APP_ICON_COLLECTION,
        self::IPHONE_5_5_8_COLLECTION,
        self::IPHONE_6_5_11_COLLECTION,
        self::IPHONE_6_7_17_COLLECTION,
        self::IPAD_PRO_3_COLLECTION,
    ];

    public const IOS_COLLECTIONS = [
        self::IOS_APP_ICON_COLLECTION,
        self::IPHONE_5_5_8_COLLECTION,
        self::IPHONE_6_5_11_COLLECTION,
        self::IPHONE_6_7_17_COLLECTION,
        self::IPAD_PRO_3_COLLECTION,
    ];

    public const ANDROID_COLLECTIONS = [
        self::ANDROID_APP_ICON_COLLECTION,
        self::ANDROID_MARKET_BANNER_COLLECTION,
        self::ANDROID_MARKET_IMAGE_COLLECTION,
    ];
}
