<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Lottery;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::inRandomOrder()->first()->id,
            'article' => Str::random(10),
            'parent_id' => null,
            'title' => fake('ru_RU')->word(),
            'description' => fake('ru_RU')->realText(30),
            'price' => null,
            'promo_price' => null,
            'discount' => null,
            'sort' => 0,
            'active' => 1,
            'popular' => 0,
            'by_order' => Lottery::odds(1, 4)->choose(),
            'has_package' => Lottery::odds(1, 2)->choose(),
        ];
    }
}
