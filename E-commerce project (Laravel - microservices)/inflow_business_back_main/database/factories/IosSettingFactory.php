<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IosSetting>
 */
class IosSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake('ru_RU')->company,
            'user_agreement_url' => fake('ru_RU')->url,
            'support_url' => fake('ru_RU')->url,
            'description' => fake('ru_RU')->realText,
            'key_words' => implode(', ',fake('ru_RU')->words(5)),
            'name' => fake('ru_RU')->name,
            'address' => fake('ru_RU')->address(),
            'email' => fake('ru_RU')->companyEmail(),
            'phone' => fake('ru_RU')->phoneNumber,
            'copyright' => fake('ru_RU')->year . ' ' . fake('ru_RU')->company,
        ];
    }
}
