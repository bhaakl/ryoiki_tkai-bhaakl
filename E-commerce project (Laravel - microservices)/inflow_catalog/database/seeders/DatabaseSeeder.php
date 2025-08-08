<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::debug('seed');
        $this->call([
            CitySeeder::class,
            StoreSeeder::class,
            OrderStatusSeeder::class,
            DeliverySeeder::class,
            DeliveryRestrictionSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            ProductCategoryProductSeeder::class,
            ProductSimilarSeeder::class,
            PropertySeeder::class,
            KitSeeder::class,
            ProductRestrictionSeeder::class,
            AcquiringSeeder::class,
        ]);
    }
}
