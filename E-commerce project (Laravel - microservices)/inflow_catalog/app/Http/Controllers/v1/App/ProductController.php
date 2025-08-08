<?php

namespace App\Http\Controllers\v1\App;

use App\Filters\ProductFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\RefreshProductsRequest;
use App\Http\Resources\v1\App\Product\ProductCollection;
use App\Http\Resources\v1\App\Product\ProductDetailResource;
use App\Http\Resources\v1\App\Product\ProductResource;
use App\Http\Resources\v1\App\Property\PropertyEnumCollection;
use App\Http\Resources\v1\App\Property\PropertyEnumResource;
use App\Http\Resources\v1\App\Property\PropertyStringCollection;
use App\Http\Resources\v1\App\Property\PropertyStringResource;
use App\Http\Resources\v1\ComponentResource;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\ProductPropertyEnum;
use App\Models\Property;
use App\Models\PropertyEnum;
use App\Models\PropertyString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request, ProductFilter $filter)
    {
        $products = Product::active()->whereHas('parent', function ($query) use ($request) {
            $query->active();
        })->filter($filter)->whereNotNull('parent_id');

        if (isset($request->search)) {
            $productIds = Product::search($request->search)->get()->pluck('id')->toArray();
            $products = $products->whereIn('id', $productIds);
        }

        foreach ($request->input() as $key => $value) {
            if (!in_array($key, ['search', 'sort', 'price_from', 'price_to', 'page', 'per_page'])) {
                /** @var Property $property */
                $property = Property::whereTitle($key)->active()->first();
                if ($property) {
                    if ($property->type == 'enum') {
                        $productIds = $property->property_enums()
                            ->join('product_property_enum', 'product_property_enum.property_enum_id', '=', 'property_enums.id')
                            ->whereIn('property_enums.id', explode(',', $value))
                            ->pluck('product_property_enum.product_id')
                            ->unique();
                        $products->where(function ($q) use ($productIds) {
                            $q->whereIn('products.id', $productIds);
                        });
                    } else {
                        $prop_string = PropertyString::find($value);
                        if (!$prop_string) {
                            $products = $products->where('id', null);
                            continue;
                        }
                        $productIds = PropertyString::whereValue($prop_string->value)->pluck('product_id')->toArray();
                        if (count($productIds) == 0)
                            $products = $products->where('id', null);
                        $products->where(function ($q) use ($productIds) {
                            $q->whereIn('id', $productIds);
                        });
                    }
                }
            }
        }

        $products = $products->paginate($request->per_page ?? self::PER_PAGE);

        return new ProductCollection($products);
    }

    public function getByIds(Request $request)
    {
        $products = Product::active()->whereHas('parent', function ($query) use ($request) {
            $query->active();
        })->whereIn('id', explode(',', $request->ids))
            ->paginate($request->per_page ?? self::PER_PAGE);

        return new ProductCollection($products);
    }

    public function search(Request $request)
    {
        $productIds = Product::search($request->search)->get()->pluck('id')->toArray();
        $childrenIds = Product::active()->whereIn('parent_id', $productIds)->pluck('id')->toArray();
        $offersIds = Product::active()->whereNotNull('parent_id')->whereIn('id', $productIds)->pluck('id')->toArray();
        $productIds = array_merge($childrenIds, $offersIds);
        $productIds = array_unique($productIds);
        $products = Product::active()
            ->whereNotNull('parent_id')
            ->whereIn('id', $productIds);
        if (count($productIds) > 0) {
            $products = $products->orderByRaw("FIELD(id, " . implode(',', $productIds) . ") ASC");
        }

        if ($request->limit) {
            $products = $products->limit($request->limit)->get();

            return ProductResource::collection($products);
        } else {
            $products = $products->paginate($request->per_page ?? self::PER_PAGE);

            return new ProductCollection($products);
        }
    }

    public function refresh(RefreshProductsRequest $request)
    {
        $products = Product::whereIn('id', $request->products)
            ->active()
            ->whereNotNull('parent_id')
            ->get();

        return ProductDetailResource::collection($products);
    }

    public function show(Product $product)
    {
        if ($product->parent_id) {
            $product = Product::find($product->parent_id);
        }

        return new ProductDetailResource($product);
    }

    public function showDescription(Product $product)
    {
        return ['text' => $product->getDescription()];
    }

    public function showCharacteristics(Product $product)
    {
        return $product->getProperties();
    }

    public function showComponents(Product $product)
    {
        if ($product->parent_id && $product->components()->count() == 0) {
            $components = $product->parent->components;
        } else {
            $components = $product->components;
        }

        return ComponentResource::collection($components);
    }

    public function showSimilar(Product $product)
    {
        $similar = $product->similar;

        return ProductDetailResource::collection($similar);
    }

    public function geFilters(Request $request)
    {
        $properties = Property::active();
        $filter = new \stdClass();
        $filter->sort = [
            [
                'name' => 'По новизне',
                'type' => 'new',
                'value' => 'created_at,desc'
            ],
            [
                'name' => 'По убыванию цены',
                'type' => 'price_desc',
                'value' => 'price,desc'
            ],
            [
                'name' => 'По возрастанию цены',
                'type' => 'price_asc',
                'value' => 'price,asc'
            ],
        ];

        if (isset($request->category) && !Category::whereId($request->category)->active()->exists()) {
            return $filter;
        }

        if (isset($request->category) && Category::whereId($request->category)->active()->exists()) {
            /** @var Category $category */
            $category = Category::find($request->category);
            $categories = $category->childrenRecursive()->pluck('id')->toArray();
            $categories[] = $request->category;
            $products_ids = CategoryProduct::whereIn('category_id', $categories)->pluck('product_id')->toArray();
            $products_ids = Product::whereIn('id', $products_ids)->active()->isOffer()->pluck('id')->toArray();
            $products_ids = array_unique($products_ids);
            $minPrice = Product::whereIn('id', $products_ids)->where('price', '>', 0)->min('price');
            $maxPrice = Product::whereIn('id', $products_ids)->where('price', '>', 0)->max('price');
        } else {
            $minPrice = Product::active()->where('price', '>', 0)->min('price');
            $maxPrice = Product::active()->where('price', '>', 0)->max('price');
            $products_ids = Product::isOffer()->active()->where('price', '>', 0)->pluck('id')->toArray();
        }
        $propertyEnums = ProductPropertyEnum::whereIn('product_id', $products_ids)->pluck('property_enum_id')->toArray();
        $propertyEnums = array_unique($propertyEnums);
        $property_ids = PropertyEnum::whereIn('id', $propertyEnums)->pluck('property_id')->toArray();
        $property_ids = array_merge($property_ids, PropertyString::whereIn('product_id', $products_ids)->pluck('property_id')->toArray());
        $property_ids = array_unique($property_ids);
        $properties = $properties->whereIn('id', $property_ids)->get()->toArray();

        if ($minPrice || $maxPrice) {
            $filter->price = [
                'name' => 'цена',
                'type' => 'range',
                'values' => [
                    'price_from' => $minPrice,
                    'price_to' => $maxPrice,
                ]
            ];
        }

        foreach ($properties as $property) {
            if ($property['type'] == 'enum') {
                $values_count = PropertyEnum::whereIn('id', $propertyEnums)->wherePropertyId($property['id'])->count();
                $propEnums = PropertyEnum::whereIn('id', $propertyEnums)->wherePropertyId($property['id'])->orderBy('value')->limit(5)->get();
                $property['has_more_values'] = $values_count > $propEnums->count();
                $property['values'] = PropertyEnumResource::collection($propEnums);
            } else {
                $vals = PropertyString::wherePropertyId($property['id'])->pluck('value')->toArray();
                $vals = array_unique($vals);
                $ids = [];
                foreach ($vals as $val) {
                    $prop = PropertyString::wherePropertyId($property['id'])->whereValue($val)->first();
                    if ($prop != null) {
                        $ids[] = $prop->id;
                    }
                }
                $values_count = PropertyString::whereIn('id', $ids)->orderBy('value')->count();
                $values = PropertyStringResource::collection(PropertyString::whereIn('id', $ids)->orderBy('value')->limit(5)->get());
                $property['has_more_values'] = $values_count > $values->count();
                $property['values'] = $values;
            }
            $property['type'] = 'multiselect';
            $filter->properties[] = $property;
        }

        return $filter;
    }

    public function getProperty($id, Request $request)
    {
        /** @var Property $property */
        $property = Property::active()->findOrFail($id);

        $products = Product::isOffer()->active();

        if (isset($request->category) && Category::whereId($request->category)->active()->exists()) {
            /** @var Category $category */
            $category = Category::find($request->category);
            $categories = $category->childrenRecursive()->pluck('id')->toArray();
            $categories[] = $request->category;
            $productsIds = CategoryProduct::whereIn('category_id', $categories)->pluck('product_id')->toArray();
            $products = $products->whereIn('id', $productsIds);
        }
        $products = $products->pluck('id')->toArray();

        if ($property->type == 'enum') {
            $productPropertyEnums = ProductPropertyEnum::whereIn('product_id', $products)->pluck('property_enum_id')->toArray();
            $propertyEnums = PropertyEnum::wherePropertyId($property->id)->whereIn('id', $productPropertyEnums)->paginate($request->per_page ?? 15);

            return new PropertyEnumCollection($propertyEnums);
        } else {
            $propertyString = PropertyString::wherePropertyId($property->id)->whereIn('product_id', $products)->paginate($request->per_page ?? 15);

            return new PropertyStringCollection($propertyString);
        }
    }
}
