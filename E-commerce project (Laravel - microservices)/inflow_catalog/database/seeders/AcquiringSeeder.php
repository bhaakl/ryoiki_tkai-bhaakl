<?php

namespace Database\Seeders;

use App\Enums\PaymentGates;
use App\Models\Acquiring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcquiringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Acquiring::create([
            'name' => PaymentGates::Payselection,
            'keys' => [
                'site_id' => encrypt('25284'),
                'secret' => encrypt('hdJgHVRFhgCr33GN'),
                'public_key' => encrypt('0492c1aee92ec3e95e6bd1d3e41e7b09abbb0ef6c64b964450f45207201db24c8de93fe820c23f8d8d0b292f43640b3cab74970375c8f071f6fac6a58d00b287fc')
            ]
        ]);
    }
}
