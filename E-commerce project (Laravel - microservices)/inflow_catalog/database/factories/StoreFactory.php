<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Lottery;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
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
            'name' => fake('ru_RU')->company(),
            'address' => fake('ru_RU')->address(),
            'phone' => fake('ru_RU')->phoneNumber(),
            'lon' => fake('ru_RU')->longitude(),
            'lat' => fake('ru_RU')->latitude(),
            'open' => 'Круглосуточно',
            'pickup' => true,
            'shop' => true,

        ];
    }
}
