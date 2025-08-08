<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\CategoryProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductCategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::debug('category_product seeding');
        $categories = Category::whereNotNull('parent_id')->pluck('id')->toArray();
        $products = Product::whereNull('parent_id')->pluck('id')->toArray();
        foreach ($categories as $category) {
            $products_count = rand(1, floor(count($products) / 2));
            shuffle($products);
            for ($i = 0; $i < $products_count; $i++) {
                CategoryProduct::create([
                    'category_id' => $category,
                    'product_id' => $products[$i]
                ]);
            }
        }
        Log::debug('category_product seeded');
    }
}
