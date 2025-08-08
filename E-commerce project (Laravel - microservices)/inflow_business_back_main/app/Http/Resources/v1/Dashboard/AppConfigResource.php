<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Http\Resources\v1\App\CityResource;
use App\Http\Resources\v1\App\MenuItemResource;
use App\Http\Resources\v1\App\NavBarItemResource;
use App\Http\Resources\v1\App\SocialLinkResource;
use App\Models\AndroidSetting;
use App\Models\AppSetting;
use App\Models\City;
use App\Models\IosSetting;
use App\Models\Media;
use App\Models\MenuItem;
use App\Models\NavBarItem;
use App\Models\SocialLink;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Tenant $model */
        $model = $this->resource;

        /** @var AppSetting $settings */
        $settings = AppSetting::first();

        $navBarItems = NavBarItem::orderBy('position')->get();

        $nab_bar = NavBarItemResource::collection($navBarItems)->toArray($request);

        $menuItems = MenuItem::orderBy('position')->get();

        $menu = MenuItemResource::collection($menuItems)->toArray($request);

        return [
            'company_name' => $model->name,
            'icon_name' => $settings->icon_name,
            'updated_at' => $settings->updated_at->timestamp,
            'state' => $settings->state->name(),
            'auth_type' => $settings->auth_type->value,
            'two_factor' => $settings->two_factor,
            'splash_screen_text' => $settings->splash_screen_text,
            'jivo' => $settings->jivo,
            'loyalty_type' => $settings->loyalty_type,
            'has_delivery' => $settings->has_delivery,
            'has_pickup' => $settings->has_pickup,
            'has_services' => $settings->has_services,
            'has_market' => $settings->has_market,
            'has_favorite' => $settings->has_favorite,
            'logo_svg' => $settings->getFirstMediaUrl(Media::LOGO_SVG_COLLECTION),
            'logo_pdf' => $settings->getFirstMediaUrl(Media::LOGO_PDF_COLLECTION),
            'logo_png' => $settings->getFirstMediaUrl(Media::LOGO_PNG_COLLECTION),
            'placeholder_logo' => config('app.url') . '/images/no_image.png',
            'menu_keys' => $menu,
            'bottom_nav_bar' => $nab_bar,
            'phone' => $model->phone,
            'email' => $model->email,
            'social_links' => SocialLinkResource::collection(SocialLink::all()),
            'android_settings' => new AndroidSettingResource(AndroidSetting::first()),
            'ios_settings' => new IosSettingResource(IosSetting::first()),
            'colors' => [
                'primary' => $settings->secondary,
                'secondary' => $settings->secondary,
                'background_1' => $settings->background_1,
                'background_2' => $settings->background_2,
                'icon' => $settings->icon,
                'text' => $settings->text,
                'gradient' => [
                    $settings->gradient_2,
                    $settings->gradient_2,
                ],
            ],
            'cities' => CityResource::collection(City::active()->get()),
        ];
    }
}
