<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyEnum>
 */
class PropertyEnumFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => mt_rand(0, 1) === 1 ? Str::of(fake('ru_RU')->realText())->words(1, '') : mt_rand(10, 100),
        ];
    }
}
