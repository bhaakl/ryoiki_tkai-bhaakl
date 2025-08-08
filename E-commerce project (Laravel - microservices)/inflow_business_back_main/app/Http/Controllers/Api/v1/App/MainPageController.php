<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Enums\LoyaltyTypes;
use App\Enums\MainPageTemplates;
use App\Http\Resources\v1\App\BannerResource;
use App\Http\Resources\v1\App\MainPageBlockResource;
use App\Http\Resources\v1\App\PromoCollection;
use App\Http\Resources\v1\App\PromoResource;
use App\Models\AppSetting;
use App\Models\Banner;
use App\Models\MainPage;
use App\Models\MainPageBlock;
use Illuminate\Http\Request;

class MainPageController extends AppController
{
    public function __construct(protected AppSetting $appSetting)
    {
        parent::__construct();
    }

    public function index()
    {
        /** @var MainPage $mainPage */
        $mainPage = MainPage::firstOrCreate();
        $blocks = [];
        if (Banner::count()) {
            $block = new \stdClass();
            $block->type = $mainPage->slider_l ? 'slider_l' : 'slider_s';
            $block->request = null;
            $block->title = 'Слайдер';
            $blocks[] = $block;
        }

        if ($mainPage->loyalty && auth('customer')->check()) {
            $block = new \stdClass();
            $block->type = 'bonus-info';
            $block->request = null;
            $block->title = 'Информация о бонусах';
            $blocks[] = $block;
        }

        if ($mainPage->my_orders && auth('customer')->check()) {
            $block = new \stdClass();
            $block->type = 'order-info';
            $block->request = null;
            $block->title = 'Мои заказы';
            $blocks[] = $block;
        }

        /** @var MainPageBlock $item */
        foreach (MainPageBlock::active()->orderBy('sort')->get() as $item) {
            $block = new \stdClass();
            $block->type = $item->template->name;
            $block->request = $item->template == MainPageTemplates::articles ? null : $item->id;
            $block->title = $item->title ?? $item->template->toString();
            if ($item->template == MainPageTemplates::products && $item->products()->count() == 0) {
                continue;
            }
            if ($item->template == MainPageTemplates::promo && $item->promos()->count() == 0) {
                continue;
            }
            $blocks[] = $block;
        }

        return api_response($blocks);
    }

    public function showItem(MainPageBlock $item, Request $request)
    {
        if ($item->template == MainPageTemplates::promo) {
            $promos = $item->promos()->active()->paginate($request->per_page ?? 15);

            return api_response(new PromoCollection($promos));
        } elseif ($item->template == MainPageTemplates::products) {
            $response = $item->getProducts($request);

            return api_response($response);
        }
    }
}
