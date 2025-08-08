<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Filters\OfferFilter;
use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AddPropertyRequest;
use App\Http\Requests\v1\ComponentAttachRequest;
use App\Http\Requests\v1\ComponentSyncRequest;
use App\Http\Requests\v1\ImageUploadRequest;
use App\Http\Requests\v1\OfferStoreRequest;
use App\Http\Requests\v1\OfferUpdateRequest;
use App\Http\Requests\v1\ProductStoreRequest;
use App\Http\Requests\v1\ProductUpdateRequest;
use App\Http\Requests\v1\RemovePropertyRequest;
use App\Http\Requests\v1\SimilarSyncRequest;
use App\Http\Requests\v1\SyncTagsRequest;
use App\Http\Resources\v1\Dashboard\Product\OfferCollection;
use App\Http\Resources\v1\Dashboard\Product\OfferDetailResource;
use App\Http\Resources\v1\Dashboard\Product\ProductCollection;
use App\Http\Resources\v1\Dashboard\Product\ProductDetailResource;
use App\Http\Resources\v1\Dashboard\Product\ProductListItemResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Component;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductPropertyEnum;
use App\Models\PropertyString;
use App\Models\Tag;
use App\Services\ProductService;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ImageTrait;

    public function __construct(protected ProductService $productService)
    {
    }

    public function index(Request $request, ProductFilter $filter)
    {
        $products = Product::filter($filter)->root();
        if (isset($request->search)) {
            $productIds = Product::search($request->search)->get()->pluck('id')->toArray();
            $products = $products->whereIn('id', $productIds);
        }
        $products = $products->paginate($request->per_page ?? self::PER_PAGE);

        return new ProductCollection($products);
    }

    public function dropDownList(Request $request)
    {
        $products = Product::root();
        if (isset($request->search)) {
            $products = $products->where('title', 'like', '%' . $request->search . '%');
        }
        $products = $products->orderBy('title')->select('id', 'title')->get();

        return ProductListItemResource::collection($products);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return new ProductDetailResource($product);
    }

    public function store(ProductStoreRequest $request)
    {
        $product = $this->productService->storeProduct($request);

        return new ProductDetailResource($product->refresh());
    }

    public function update($id, ProductUpdateRequest $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);
        $product->update($request->validated());
        if (!$product->parent_id) {
            Product::whereParentId($product->id)->update(['by_order' => $product->by_order]);
        }
        $original = $product;
        if ($product->parent_id) {
            $product = $product->parent;
        }
        $product->categories()->sync($request->categories);
        foreach ($product->offers as $offer) {
            $offer->categories()->sync($request->categories);
        }

        return new ProductDetailResource($original->refresh());
    }

    public function destroy($id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);
        $product->offers()->delete();
        $product->delete();

        return response()->json(['message' => 'ok']);
    }

    public function uploadImage(ImageUploadRequest $request, string $id)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);
        $collection = $request->main ? 'main' : 'extra';
        $media = $this->uploadPhoto($request->file('image'), $product, $collection);

        return new MediaResource($media);
    }

    public function deleteImage(string $id, string $uuid)
    {
        Media::whereUuid($uuid)->delete();

        return response()->json(['message' => 'ok']);
    }

    public function offers($id, OfferFilter $filter)
    {
        /** @var Product $product */
        Product::findOrFail($id);

        $offers = Product::whereParentId($id)
            ->filter($filter)
            ->paginate($request->per_page ?? self::PER_PAGE);

        return new OfferCollection($offers);
    }

    public function storeOffer($id, OfferStoreRequest $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $offer = Product::create($request->validated());
        $offer->update(['parent_id' => $product->id]);

        return new OfferDetailResource($offer);
    }

    public function showOffer($id, $offer)
    {
        /** @var Product $product */
        Product::findOrFail($id);

        /** @var Product $offer */
        $offer = Product::whereParentId($id)->whereId($offer)->firstOrFail();

        return new OfferDetailResource($offer);
    }

    public function updateOffer($id, $offer, OfferUpdateRequest $request)
    {
        /** @var Product $product */
        Product::findOrFail($id);

        /** @var Product $offer */
        $offer = Product::whereParentId($id)->whereId($offer)->firstOrFail();
        $this->productService->updateOffer($offer, $request);

        return new OfferDetailResource($offer);
    }

    public function destroyOffer($id, $offer)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        /** @var Product $offer */
        $offer = Product::whereParentId($id)->whereId($offer)->firstOrFail();

        if ($product->offers()->count() < 2) {
            return response()->json('Нельзя удалять единственное торговое предложение', 400);
        }
        $offer->delete();

        return response()->json(['message' => 'ok']);
    }

    public function updateProperties($id, AddPropertyRequest $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        $product->property_enums()->sync($request->enums);
        $property_ids = [];
        foreach ($request->strings as $string) {
            $property_ids[] = $string['property_id'];
            PropertyString::updateOrCreate([
                'product_id' => $product->id,
                'property_id' => $string['property_id'],
            ], [
                'value' => $string['value'],
            ]);
        }
        PropertyString::whereProductId($product->id)->whereNotIn('property_id', $property_ids)->delete();

        if ($product->parent_id) {
            return new OfferDetailResource($product);
        }

        return new ProductDetailResource($product);
    }

    public function removeProperty($id, RemovePropertyRequest $request)
    {
        if (!Product::whereId($id)->exists()) {
            abort(404, 'Товар не найден');
        }

        if ($request->type == 'string') {
            PropertyString::whereId($request->id)->where('product_id', $id)->delete();
        } else {
            ProductPropertyEnum::whereId($request->id)->where('product_id', $id)->delete();
        }

        return response()->json(['message' => 'ok']);
    }

    public function addTag($id, $tag)
    {
        if (!Product::whereId($id)->root()->exists()) {
            abort(404, 'Товар не найден');
        }

        if (!Tag::whereId($tag)->exists()) {
            abort(404, 'Тэг не найден');
        }

        $product = Product::find($id);

        if (!$product->tags->contains(Tag::find($tag))) {
            $product->tags()->attach($tag);
        }

        return new ProductDetailResource($product);
    }

    public function syncTags($id, SyncTagsRequest $request)
    {
        if (!Product::whereId($id)->root()->exists()) {
            abort(404, 'Товар не найден');
        }

        /** @var Product $product */
        $product = Product::find($id);
        $product->tags()->sync($request->tags);

        return new ProductDetailResource($product);
    }

    public function removeTag($id, $tag)
    {
        if (!Product::whereId($id)->root()->exists()) {
            abort(404, 'Товар не найден');
        }

        if (!Tag::whereId($tag)->exists()) {
            abort(404, 'Тэг не найден');
        }

        $product = Product::find($id);

        $product->tags()->detach($tag);

        return new ProductDetailResource($product);
    }

    public function attachComponent($id, ComponentAttachRequest $request)
    {
        /** @var Product $product */
        $product = Product::findOrFail($id);

        if (!$product->components->contains(Component::find($request->component_id))) {
            $product->components()->attach($request->component_id, [
                'quantity' => $request->quantity,
                'unit' => $request->unit
            ]);
        }

        return new ProductDetailResource($product->refresh());
    }

    public function syncComponents($id, ComponentSyncRequest $request)
    {
        /** @var Product $product */
        $product = Product::find($id);

        $product->components()->detach();
        foreach ($request->components as $component) {
            $product->components()->attach($component['id'], [
                'quantity' => $component['quantity'],
                'unit' => $component['unit']
            ]);
        }

        return new ProductDetailResource($product->refresh());
    }

    public function detachComponent($id, $component)
    {
        if (!Product::whereId($id)->exists()) {
            abort(404, 'Товар не найден');
        }
        if (!Component::whereId($component)->exists()) {
            abort(404, 'Компонент не найден');
        }

        $product = Product::find($id);
        $product->components()->detach($component);

        return new ProductDetailResource($product->refresh());
    }

    public function showSimilar($id, Request $request)
    {
        if (!Product::whereId($id)->root()->exists()) {
            abort(404, 'Товар не найден');
        }
        /** @var Product $product */
        $product = Product::find($id);

        $similar = $product->similar();
        if ($request->search) {
            $similar = $similar->where('title', 'like', "%$request->search%");
        }

        $similar = $similar->select('products.id', 'products.title')->orderBy('title')->get();

        return ProductListItemResource::collection($similar);
    }

    public function syncSimilar($id, SimilarSyncRequest $request)
    {
        if (!Product::whereId($id)->root()->exists()) {
            abort(404, 'Товар не найден');
        }
        /** @var Product $product */
        $product = Product::find($id);

        $product->similar()->sync($request->similar);

        return ProductListItemResource::collection($product->similar);
    }

    public function detachSimilar($id, $similar)
    {
        if (!Product::whereId($id)->root()->exists()) {
            abort(404, 'Товар не найден');
        }
        /** @var Product $product */
        $product = Product::find($id);

        $product->similar()->detach($similar);

        return ProductListItemResource::collection($product->similar);
    }
}
