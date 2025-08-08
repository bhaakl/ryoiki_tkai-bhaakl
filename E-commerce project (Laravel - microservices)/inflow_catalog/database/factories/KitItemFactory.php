<?php

namespace Database\Factories;

use App\Enums\DiscountTypes;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Lottery;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KitItem>
 */
class KitItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discount_type = mt_rand(1, 2) === 1 ? DiscountTypes::PERCENT : DiscountTypes::RUB;
        $discount_value = $discount_type === DiscountTypes::PERCENT ? mt_rand(5, 30) : mt_rand(100, 300);

        return [
            'product_id' => Product::active()->whereNotNull('parent_id')->inRandomOrder()->first()->id,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'alt_category_id' => Lottery::odds(1, 2)->choose() ? Category::active()->whereNotNull('parent_id')->inRandomOrder()->first()->id : null,
        ];
    }
}
