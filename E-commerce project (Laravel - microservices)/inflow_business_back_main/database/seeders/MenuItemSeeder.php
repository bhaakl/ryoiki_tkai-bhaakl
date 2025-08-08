<?php

namespace Database\Seeders;

use App\Enums\MenuKeyIcons;
use App\Enums\MenuKeyTemplates;
use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = MenuKeyTemplates::cases();

        foreach ($values as $value) {
            if ($value->isCustom() && !app()->isProduction()) {
                MenuItem::firstOrCreate([
                    'value' => $value->value,
                    'title' => "Условия доставки",
                    'content' => fake('ru_RU')->realText(),
                    'icon' => MenuKeyIcons::Truck
                ]);
                MenuItem::firstOrCreate([
                    'value' => $value->value,
                    'title' => "Ещё какая-то кнопка",
                    'content' => fake('ru_RU')->realText(),
                    'icon' => MenuKeyIcons::Truck
                ]);
            } else {
                MenuItem::firstOrCreate([
                    'value' => $value->value,
                    'title' => $value->systemName(),
                    'icon' => MenuKeyIcons::Truck
                ]);
            }
        }

        foreach (MenuItem::all() as $item) {
            try {
                $item->addMediaFromUrl('https://loremflickr.com/300/300')->toMediaCollection('icon');
            } catch (\Exception $exception) {}
        }
    }
}
