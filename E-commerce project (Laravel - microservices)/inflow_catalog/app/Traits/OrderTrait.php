<?php

namespace App\Traits;

use App\Enums\DiscountTypes;
use App\Models\Kit;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

trait OrderTrait
{
    public function calculateCart($products = [], $kits = [])
    {
        $total = 0;
        if (isset($products)) {
            foreach ($products as $cart_product) {
                /** @var Product $product */
                $product = Product::find($cart_product['id']);
                $price = ($product->promo_price ?? $product->price) * $cart_product['quantity'];
                $total += $price;
            }
        }
        if (isset($kits)) {
            foreach ($kits as $kit_index => $cart_kit) {
                /** @var Kit $kit */
                $kit = Kit::find($cart_kit['id']);
                if ($kit->items()->count() != count($cart_kit['products'])) {
                    $error = \Illuminate\Validation\ValidationException::withMessages([
                        'kits.' . $kit_index . '.products' => __('validation.custom.array_length'),
                    ]);
                    throw $error;
                }
                foreach ($kit->items as $index => $kit_item) {
                    /** @var Product $product */
                    $product = Product::find($cart_kit['products'][$index]);
                    $price = $kit_item->discount_type == DiscountTypes::RUB ?
                        $product->price - $kit_item->discount_value :
                        floor($product->price - $product->price / 100 * $kit_item->discount_value);
                    $price = $price < 0 ? 0 : $price;
                    $price *= $cart_kit['quantity'];
                    $total += $price;
                }
            }
        }

        return $total;
    }
}
