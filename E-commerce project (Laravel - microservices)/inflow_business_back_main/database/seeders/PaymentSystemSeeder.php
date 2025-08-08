<?php

namespace Database\Seeders;

use App\Enums\PaymentIcons;
use App\Enums\PaymentSystems;
use App\Models\PaymentSystem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentSystem::create([
            'type' => PaymentSystems::ONLINE->value,
            'name' => 'Картой',
            'description' => 'онлайн',
            'icon' => PaymentIcons::CONTACTLESS->value,
            'platform_commission' => 2.5
        ]);
    }
}
