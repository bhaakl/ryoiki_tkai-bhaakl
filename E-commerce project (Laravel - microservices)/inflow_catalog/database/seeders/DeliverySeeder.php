<?php

namespace Database\Seeders;

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
use App\Models\City;
use App\Models\Delivery;
use App\Models\DeliveryInterval;
use App\Models\DeliveryPriceCondition;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $intervals = [
            '08:00-10:00',
            '10:00-12:00',
            '12:00-14:00',
            '14:00-16:00',
            '16:00-18:00',
            '18:00-20:00',
            '20:00-22:00',
        ];

        foreach (City::all() as $city) {
            $pickup = Delivery::create([
                'city_id' => $city->id,
                'type' => DeliveryTypes::PICKUP,
                'icon' => null,
                'name' => 'Самовывоз',
                'description' => 'Будет трудно, тяжело да и погода мерзкая, зато бесплатно',
                'base_cost' => 0,
                'has_intervals' => false,
                'active' => true,
            ]);

            $courier = Delivery::create([
                'city_id' => $city->id,
                'type' => DeliveryTypes::DELIVERY,
                'icon' => DeliveryIcons::TRUCK,
                'name' => 'Курьерская доставка',
                'description' => 'Наш курьер доставит в любую точку города',
                'base_cost' => 500,
                'has_intervals' => true,
                'active' => true,
            ]);

            Delivery::create([
                'city_id' => $city->id,
                'type' => DeliveryTypes::DELIVERY,
                'icon' => DeliveryIcons::PACKAGE,
                'name' => 'Доставка по почте',
                'description' => 'Отправка по всей стране',
                'base_cost' => 800,
                'has_intervals' => false,
                'active' => true,
            ]);

            foreach ($intervals as $interval) {
                $times = explode('-', $interval);
                DeliveryInterval::create([
                    'delivery_id' => $courier->id,
                    'interval' => $interval,
                    'time_from' => Carbon::parse($times[0]),
                    'time_to' => Carbon::parse($times[1]),
                ]);
            }

            foreach (Store::whereCityId($city->id)->get() as $store) {
                $pickup->stores()->attach($store->id);
            }

            foreach (Delivery::where('base_cost', '>', 0)->get() as $delivery) {
                for ($i = 2; $i < 5; $i++) {
                    $condition = DeliveryPriceCondition::create([
                        'delivery_id' => $delivery->id,
                        'min_amount' => $i * 1000,
                        'max_amount' => $i * 2000 - 1,
                        'price' => $delivery->base_cost - ($i * 100),
                    ]);
                }
                $condition->update(['max_amount' => null]);
            }
        }
    }
}
