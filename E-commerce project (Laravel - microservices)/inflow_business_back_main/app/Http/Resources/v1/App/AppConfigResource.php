<?php

namespace App\Http\Resources\v1\App;

use App\Models\AppSetting;
use App\Models\City;
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

        $menuItems = MenuItem::active()->orderBy('position')->get();

        $menu = MenuItemResource::collection($menuItems)->toArray($request);

        return [
            'updated_at' => $settings->updated_at->timestamp,
            'yandex_map_key' => config('map.api_key'),
            'state' => $settings->state->name(),
            'auth_type' => $settings->auth_type->value,
            'two_factor' => $settings->two_factor,
            'jivo' => $settings->jivo,
            'splash_screen_text' => $settings->splash_screen_text,
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
            'colors' => [
                'primary' => $settings->primary,
                'white' => '#FFFFFFFF',
                'text_title' => '#FF171717',
                'text_black' => '#FF171717',
                'text_gray_dark' => '#FF454545',
                'text_gray_light' => '#FF747474',
                'text_gray_extra_light' => '#FFA1A1A1',
                'text_gray_extra_light_2' => '#FFCBCBCB',
                'text_white' => '#FFFFFFFF',
                'text_red' => $settings->text,
                'border_dark' => '#FF454545',
                'border_light' => '#FFDEDDDD',
                'border_extra_light' => '#FFE9E9EC',
                'background_gray_dark' => '#FFE9E9EC',
                'background_gray' => '#FFF3F6F8',
                'background_white' => '#FFFFFFFF',
                'background_white_opacity_1' => '#99FFFFFF',
                'background_white_opacity_2' => '#29FFFFFF',
                'background_green' => '#FFF0F8EF',
                'background_powder' => $settings->background_1,
                'background_powder_dark' => $settings->background_2,
                'icon_black' => '#FF171717',
                'icon_white' => '#FFFFFFFF',
                'icon_gray_light' => '#FF747474',
                'icon_gray_extra_light' => '#FFA1A1A1',
                'icon_gray_extra_light_2' => '#FFCBCBCB',
                'icon_red' => $settings->icon,
                'secondary_red' => '#FFEF8078',
                'secondary_red_dark' => $settings->secondary,
                'secondary_green' => '#FF6EBD61',
                'secondary_orange' => '#FFF08734',
                'gradient' => [
                    $settings->gradient_1,
                    $settings->gradient_2,
                ],

            ],
            'cities' => CityResource::collection(City::active()->get()),
        ];
    }
}
