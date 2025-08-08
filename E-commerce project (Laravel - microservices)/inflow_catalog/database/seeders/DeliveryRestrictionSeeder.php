<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryRestriction;
use App\Models\DeliveryRestrictionDeliveryInterval;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DeliveryRestrictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deliveries = Delivery::all();
        Log::debug('seeder', [
            'deliveries' => $deliveries->toArray(),
        ]);

        foreach ($deliveries as $delivery) {
            $delivery_restriction = DeliveryRestriction::create([
                'delivery_id' => $delivery->id,
                'date_from' => now()->addDays(10),
                'date_to' => now()->addDays(10),
            ]);

            if ($delivery->has_intervals) {
                $intervals = $delivery->intervals()
                    ->inRandomOrder()
                    ->limit(mt_rand($delivery->intervals()->count() - 2, $delivery->intervals()->count()))
                    ->get();
                foreach ($intervals as $interval) {
                    DeliveryRestrictionDeliveryInterval::create([
                        'delivery_restriction_id' => $delivery_restriction->id,
                        'delivery_interval_id' => $interval->id,
                    ]);
                }
            }
        }
    }
}
