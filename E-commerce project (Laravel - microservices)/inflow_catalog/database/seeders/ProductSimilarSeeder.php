<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSimilarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        foreach ($products as $product) {
            $similars = Product::whereNotNull('parent_id')
                ->where('id', '!=', $product->id)
                ->where('parent_id', '!=', $product->parent_id)
                ->inRandomOrder()
                ->limit(mt_rand(1,4))
                ->pluck('id')
                ->toArray();
            $product->similar()->sync($similars);
        }
    }
}
