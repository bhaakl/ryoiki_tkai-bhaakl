<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPropertyEnum;
use App\Models\Property;
use App\Models\PropertyEnum;
use App\Models\PropertyString;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::factory(10)->create();
        $properties = Property::pluck('id')->toArray();
        foreach ($properties as $property) {
            $products = Product::inRandomOrder()->limit(5)->pluck('id')->toArray();
            foreach ($products as $product) {
                PropertyString::create([
                    'property_id' => $property,
                    'product_id' => $product,
                    'value' => mt_rand(0, 1) === 1 ? Str::of(fake('ru_RU')->realText())->words(1, '') : mt_rand(10, 100),
                ]);
            }
        }
        Property::factory(10)->create(['type' => 'enum']);
        $properties = Property::whereType('enum')->pluck('id')->toArray();
        foreach ($properties as $property) {
            PropertyEnum::factory(5)->create(['property_id' => $property]);
        }
        $property_enums = PropertyEnum::all();
        foreach ($property_enums as $property_enum) {
            $products = Product::inRandomOrder()->limit(5)->pluck('id')->toArray();
            foreach ($products as $product) {
                ProductPropertyEnum::create([
                    'property_enum_id' => $property_enum->id,
                    'product_id' => $product
                ]);
            }
        }
    }
}
