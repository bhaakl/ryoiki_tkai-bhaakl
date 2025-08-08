<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Category;
use App\Traits\ImageTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductCategorySeeder extends Seeder
{
    use ImageTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::debug('CategorySeeder');
        Category::factory(3)->create();
        $parents = Category::all();
        /** @var Category $parent */
        foreach ($parents as $parent) {
            if (app()->environment() !== 'testing') {
                try {
                    $parent->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection();
                } catch (\Exception $exception) {}
            }
            Category::factory(2)->create(['parent_id' => $parent->id]);
            $children = Category::whereParentId($parent->id)->get();
            /** @var Category $child */
            foreach ($children as $child) {
                if (app()->environment() !== 'testing') {
                    try {
                        $child->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection();
                        sleep(1);
                    } catch (\Exception $exception) {}
                }
            }
        }
        Log::debug('CategorySeeder done');
    }
}
