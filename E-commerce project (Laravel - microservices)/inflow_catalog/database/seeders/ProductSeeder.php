<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Product;
use App\Traits\ImageTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Lottery;

class ProductSeeder extends Seeder
{
    use ImageTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::debug('product seeding');
        Product::factory(10)->create();
        Log::debug('root products ready');
        $main_products = Product::all();
        /** @var Product $product */
        foreach ($main_products as $product) {
            if (app()->environment() !== 'testing') {
                try {
                    $product->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('main');
                } catch (\Exception $exception) {}
                sleep(1);
                try {
                    $product->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('extra');
                } catch (\Exception $exception) {}
                try {
                    $product->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('extra');
                } catch (\Exception $exception) {}
            }
            $price = fake('ru_RU')->numberBetween(800, 1000);
            $promo_price = rand(0, 1);
            $promo_price = $promo_price ? fake('ru_RU')->numberBetween(600, 800) : null;
            $discount = $promo_price ? 100 - ($promo_price / $price * 100) : null;
            Product::factory(rand(1, 2))->create([
                'city_id' => $product->city_id,
                'parent_id' => $product->id,
                'price' => $price,
                'promo_price' => $promo_price,
                'discount' => $discount,
                'popular' => Lottery::odds(1, 2)->choose(),
            ]);
        }
        Log::debug('product seeded');
    }
}
