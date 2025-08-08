<?php

namespace Database\Factories;

use App\Enums\AppCategories;
use App\Enums\Languages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AndroidSetting>
 */
class AndroidSettingFactory extends Factory
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
            'short_description' => fake('ru_RU')->realText(50),
            'description' => fake('ru_RU')->realText(300),
            'user_agreement_url' => fake('ru_RU')->url,
            'user_delete_form_url' => fake('ru_RU')->url,
            'default_language' => Languages::RU,
            'app_category' => AppCategories::ESTORE,
        ];
    }
}
