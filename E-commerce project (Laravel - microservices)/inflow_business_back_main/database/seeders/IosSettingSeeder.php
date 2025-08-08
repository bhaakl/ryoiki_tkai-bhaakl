<?php

namespace Database\Seeders;

use App\Models\IosSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IosSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = IosSetting::factory()->create();
        try {
            $setting->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('app_icon');
            $setting->addMediaFromUrl('https://loremflickr.com/1242/2208')->toMediaCollection('iphone5_5-8');
            $setting->addMediaFromUrl('https://loremflickr.com/1242/2208')->toMediaCollection('iphone5_5-8');
            $setting->addMediaFromUrl('https://loremflickr.com/1242/2688')->toMediaCollection('iphone6_5-11');
            $setting->addMediaFromUrl('https://loremflickr.com/1242/2688')->toMediaCollection('iphone6_5-11');
            $setting->addMediaFromUrl('https://loremflickr.com/1242/2688')->toMediaCollection('iphone6_7-14');
            $setting->addMediaFromUrl('https://loremflickr.com/1242/2688')->toMediaCollection('iphone6_7-14');
            $setting->addMediaFromUrl('https://loremflickr.com/2048/2732')->toMediaCollection('ipad_pro_3');
            $setting->addMediaFromUrl('https://loremflickr.com/2048/2732')->toMediaCollection('ipad_pro_3');
        } catch (\Exception $exception) {}
    }
}
