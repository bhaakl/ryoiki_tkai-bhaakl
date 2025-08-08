<?php

namespace Database\Seeders;

use App\Models\NavBarItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavBarItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NavBarItem::firstOrCreate([
            'value' => 'home'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => false,
            'icon' => '/images/icons/navbar/home.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'menu'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => false,
            'icon' => '/images/icons/navbar/profile.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'catalog'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => true,
            'icon' => '/images/icons/navbar/shop.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'cart'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => true,
            'icon' => '/images/icons/navbar/bag.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'catalog_market'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => true,
            'icon' => '/images/icons/navbar/receipt-item.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'market'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => true,
            'icon' => '/images/icons/navbar/shop.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'favorite'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => true,
            'icon' => '/images/icons/navbar/heart.png'
        ]);
        NavBarItem::firstOrCreate([
            'value' => 'workers'
        ], [
            'position' => 0,
            'active' => true,
            'switchable' => true,
            'icon' => '/images/icons/navbar/profile-2user.png'
        ]);
    }
}
