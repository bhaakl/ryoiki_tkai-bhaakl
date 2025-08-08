<?php

namespace Database\Seeders;

use App\Enums\OrderStatuses;
use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (OrderStatuses::cases() as $case) {
            OrderStatus::firstOrCreate([
                'code' => $case->value
            ], [
                'name' => $case->name
            ]);
        }
    }
}
