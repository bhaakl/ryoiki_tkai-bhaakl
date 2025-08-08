<?php

namespace Database\Seeders;

use App\Models\AndroidSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AndroidSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = AndroidSetting::factory()->create();

        try {
            $setting->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('app_icon');
            $setting->addMediaFromUrl('https://loremflickr.com/1024/500')->toMediaCollection('market_banner');
            $setting->addMediaFromUrl('https://loremflickr.com/540/960')->toMediaCollection('market_image');
            $setting->addMediaFromUrl('https://loremflickr.com/540/960')->toMediaCollection('market_image');
        } catch (\Exception $exception) {}
    }
}
