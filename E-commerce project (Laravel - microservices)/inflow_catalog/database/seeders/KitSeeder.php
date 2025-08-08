<?php

namespace Database\Seeders;

use App\Models\Kit;
use App\Models\KitItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kit::factory(10)->create();

        foreach (Kit::all() as $kit) {
            KitItem::factory(mt_rand(2, 4))->create(['kit_id' => $kit->id]);
        }
    }
}
