<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PropertyString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function storeProduct($request): Product
    {
        DB::beginTransaction();
        /** @var Product $product */
        $product = Product::create($request->validated());
        $product->categories()->sync($request->categories);
        $this->addRelated($product, $request);

        if ($request->simple) {
            if ($product->promo_price) {
                $discount = floor($product->promo_price / $product->price * 100);
                $product->update(['discount' => $discount]);
            } elseif ($product->discount) {
                $promo_price = $product->price - ($product->discount * $product->price / 100);
                $product->update(['promo_price' => $promo_price]);
            }

            $offer = $product->replicate();
            $offer->parent_id = $product->id;
            $offer->save();

            $this->addRelated($offer, $request);

            $product->update([
                'price' => null,
                'promo_price' => null,
                'discount' => null,
                'bonus' => null,
                'barcode' => null,
            ]);
        }
        DB::commit();


        return $product;
    }

    public function updateOffer(Product $offer, Request $request): Product
    {
        $offer->update($request->validated());
        if ($request->promo_price) {
            $discount = 100 - floor($offer->promo_price / $offer->price * 100);
            $offer->update(['discount' => $discount]);
        } elseif ($request->discount) {
            $promo_price = $offer->price - ($offer->discount * $offer->price / 100);
            $offer->update(['promo_price' => $promo_price]);
        }

        return $offer;
    }

    private function addRelated(Product $product, $request)
    {
        if ($request->components) {
            foreach ($request->components as $component) {
                $product->components()->attach($component['id'], [
                    'quantity' => $component['quantity'],
                    'unit' => $component['unit']
                ]);
            }
        }
        if ($request->enums) {
            $product->property_enums()->sync($request->enums);
        }
        if ($request->strings) {
            foreach ($request->strings as $string) {
                PropertyString::updateOrCreate([
                    'product_id' => $product->id,
                    'property_id' => $string['property_id'],
                ], [
                    'value' => $string['value'],
                ]);
            }
        }
    }
}
