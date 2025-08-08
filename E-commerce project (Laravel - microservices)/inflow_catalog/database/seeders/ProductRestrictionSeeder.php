<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryInterval;
use App\Models\DeliveryProductRestriction;
use App\Models\DeliveryProductRestrictionInterval;
use App\Models\Product;
use App\Models\ProductRestriction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Lottery;

class ProductRestrictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::inRandomOrder()->whereNotNull('parent_id')->limit(10)->pluck('id')->toArray();
        foreach ($products as $product) {
            //Полный запрет на все доставки в эту дату
            ProductRestriction::create([
                'date' => Carbon::createFromDate(2024, 11, 20),
                'product_id' => $product,
                'total' => true
            ]);

            //Запрет на один вид доставки на весь день в эту дату
            $product_restriction = ProductRestriction::create([
                'date' => Carbon::createFromDate(2024, 11, 19),
                'product_id' => $product,
                'total' => false
            ]);

            $delivery = Delivery::whereHasIntervals(false)->inRandomOrder()->first();

            DeliveryProductRestriction::create([
                'delivery_id' => $delivery->id,
                'product_restriction_id' => $product_restriction->id,
                'all_day' => true
            ]);

            //Запрет на некоторые интервалы доставки в эту дату
            $product_restriction = ProductRestriction::create([
                'date' => Carbon::createFromDate(2024, 11, 18),
                'product_id' => $product,
                'total' => false
            ]);

            /** @var Delivery $delivery */
            $delivery = Delivery::whereHasIntervals(true)->inRandomOrder()->first();

            $delivery_product_restriction = DeliveryProductRestriction::create([
                'delivery_id' => $delivery->id,
                'product_restriction_id' => $product_restriction->id,
                'all_day' => Lottery::odds(1, 2)->choose()
            ]);

            if ($delivery_product_restriction->all_day) {
                $intervals = DeliveryInterval::whereDeliveryId($delivery->id)
                    ->inRandomOrder()
                    ->limit(mt_rand($delivery->intervals()->count() - 2, $delivery->intervals()->count()))
                    ->get();

                foreach ($intervals as $interval) {
                    DeliveryProductRestrictionInterval::create([
                        'dpr_id' => $delivery_product_restriction->id,
                        'di_id' => $interval->id
                    ]);
                }
            }
        }
    }
}
