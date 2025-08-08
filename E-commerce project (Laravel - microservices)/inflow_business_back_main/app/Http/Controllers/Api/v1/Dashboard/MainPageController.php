<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\MainPageTemplates;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\MainPageBlockCreateRequest;
use App\Http\Requests\v1\Dashboard\MainPageBlockUpdateRequest;
use App\Http\Requests\v1\Dashboard\MainPageProductCreateRequest;
use App\Http\Requests\v1\Dashboard\MainPageProductUpdateRequest;
use App\Http\Requests\v1\Dashboard\MainPageUpdateRequest;
use App\Http\Requests\v1\Dashboard\PromoCreateRequest;
use App\Http\Requests\v1\Dashboard\PromoUpdateRequest;
use App\Http\Requests\v1\Dashboard\PromoUploadImageRequest;
use App\Http\Resources\v1\Dashboard\MainPageBlockResource;
use App\Http\Resources\v1\Dashboard\MainPageProductResource;
use App\Http\Resources\v1\Dashboard\MainPageResource;
use App\Http\Resources\v1\Dashboard\MainPageTemplateResource;
use App\Http\Resources\v1\Dashboard\PromoDetailResource;
use App\Http\Resources\v1\Dashboard\PromoResource;
use App\Models\MainPage;
use App\Models\MainPageBlock;
use App\Models\MainPageProduct;
use App\Models\Promo;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ChiefProductService;
use App\Traits\ImageTrait;

class MainPageController extends Controller
{
    use ImageTrait;

    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function index()
    {
        /** @var MainPage $mainPage */
        $mainPage = MainPage::firstOrCreate();

        return api_response(new MainPageResource($mainPage));
    }

    public function update(MainPageUpdateRequest $request)
    {
        /** @var MainPage $mainPage */
        $mainPage = MainPage::firstOrCreate();

        $mainPage->update($request->validated());

        return api_response(new MainPageResource($mainPage->refresh()));
    }

    public function templates()
    {
        $templates = MainPageTemplates::cases();
        $templates = array_filter($templates, fn($template) => $template->addable());

        return api_response(MainPageTemplateResource::collection($templates));
    }

    public function blocks()
    {
        $blocks = MainPageBlock::where('template', '!=', 'banners')->get();

        return api_response(MainPageBlockResource::collection($blocks));
    }

    public function storeBlock(MainPageBlockCreateRequest $request)
    {
        $main_page_block = MainPageBlock::create($request->validated());

        return api_response(new MainPageBlockResource($main_page_block));
    }

    public function updateBlock($id, MainPageBlockUpdateRequest $request)
    {
        $item = MainPageBlock::findOrFail($id);

        $item->update($request->validated());

        return api_response(new MainPageBlockResource($item));
    }

    public function deleteBlock($id)
    {
        $item = MainPageBlock::findOrFail($id);
        $item->delete();

        return api_response(['message' => 'ok']);
    }

    public function storePromo($id, PromoCreateRequest $request)
    {
        /** @var MainPageBlock $block */
        $block = MainPageBlock::findOrFail($id);

        if ($block->template != MainPageTemplates::promo) {
            return api_error('Блок указан не верно');
        }

        /** @var Promo $promo */
        $promo = $block->promos()->create($request->validated());

        $this->uploadPhoto($request->file('main_image'), $promo, 'main');
        foreach ($request->file('slides') as $image) {
            $this->uploadPhoto($image, $promo, 'slides');
        }

        return api_response(new PromoDetailResource($promo->refresh()));
    }

    public function showPromo($id, $promo)
    {
        /** @var MainPageBlock $block */
        $block = MainPageBlock::findOrFail($id);

        /** @var Promo $promo */
        $promo = Promo::findOrFail($promo);

        return api_response(new PromoDetailResource($promo));
    }

    public function updatePromo($id, $promo, PromoUpdateRequest $request)
    {
        /** @var MainPageBlock $block */
        $block = MainPageBlock::findOrFail($id);

        /** @var Promo $promo */
        $promo = Promo::findOrFail($promo);

        $promo->update($request->validated());

        return api_response(new PromoDetailResource($promo->refresh()));
    }

    public function uploadImage($id, $promo, PromoUploadImageRequest $request)
    {
        /** @var MainPageBlock $block */
        $block = MainPageBlock::findOrFail($id);

        /** @var Promo $promo */
        $promo = Promo::findOrFail($promo);

        if ($request->hasFile('main_image')) {
            $this->uploadPhoto($request->file('main_image'), $promo, 'main');
        }
        if ($request->slides) {
            foreach ($request->file('slides') as $image) {
                $this->uploadPhoto($image, $promo, 'slides');
            }
        }

        return api_response(new PromoDetailResource($promo->refresh()));
    }

    public function destroyPromo($id, $promo)
    {
        /** @var MainPageBlock $block */
        $block = MainPageBlock::findOrFail($id);

        /** @var Promo $promo */
        $promo = Promo::findOrFail($promo);

        $promo->delete();

        return api_response(['message' => 'ok']);
    }

    public function storeProduct($id, MainPageProductCreateRequest $request, ChiefProductService $productService)
    {
        /** @var MainPageBlock $block */
        MainPageBlock::findOrFail($id);

        $product = $productService->show($request->product_id);
        if ($product->status() != 200) {
            return api_error($product->reason());
        }
        $product = $product->object();
        $main_page_product = MainPageProduct::updateOrCreate([
            'main_page_block_id' => $id,
            'product_id' => $product->id,
        ], [
            'title' => $product->title,
            'is_active' => true,
        ]);

        return api_response(new MainPageProductResource($main_page_product));
    }

    public function updateProduct($id, $product, MainPageProductUpdateRequest $request, ChiefProductService $productService)
    {
        /** @var MainPageBlock $block */
        MainPageBlock::findOrFail($id);

        /** @var MainPageProduct $main_page_product */
        $main_page_product = MainPageProduct::findOrFail($product);

        if ($request->product_id) {
            $product = $productService->show($request->product_id);
            if ($product->status() != 200) {
                return api_error($product->reason());
            }
            $product = $product->object();
            $main_page_product->update([
                'product_id' => $product->id,
                'title' => $product->title,
            ]);
        }
        if (isset($request->is_active)) {
            $main_page_product->update([
                'is_active' => $request->is_active,
            ]);
        }

        return api_response(new MainPageProductResource($main_page_product));
    }

    public function destroyProduct($id, $product)
    {
        /** @var MainPageBlock $block */
        MainPageBlock::findOrFail($id);

        /** @var MainPageProduct $main_page_product */
        $main_page_product = MainPageProduct::findOrFail($product);

        $main_page_product->delete();

        return api_response(['message' => 'ok']);
    }
}
