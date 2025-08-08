<?php

namespace Database\Seeders;

use App\Enums\MainPageTemplates;
use App\Models\Article;
use App\Models\Banner;
use App\Models\MainPage;
use App\Models\MainPageBlock;
use App\Models\MainPageProduct;
use App\Models\Promo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainpage = MainPage::firstOrCreate();

        foreach (MainPageTemplates::cases() as $template) {
            if ($template == MainPageTemplates::banners) {
                for ($i = 0; $i <= 3; $i++) {
                    /** @var Banner $banner */
                    $banner = Banner::create([
                        'title' => fake('ru_ru')->realText(30),
                        'category_id' => mt_rand(1, 10)
                    ]);
                    try {
                        $banner->addMediaFromUrl('https://loremflickr.com/1024/500')->toMediaCollection();
                    } catch (\Exception $exception) {}
                }
            }
            /** @var MainPageBlock $item */
            $item = MainPageBlock::create([
                'template' => $template,
                'title' => $template->toString(),
            ]);
            if ($template == MainPageTemplates::promo) {
                for ($i = 0; $i <= 3; $i++) {
                    /** @var Promo $promo */
                    $promo = Promo::create([
                        'main_page_block_id' => $item->id,
                        'title' => fake('ru_ru')->realText(30),
                        'description' => fake('ru_ru')->realText(),
                        'category_id' => mt_rand(1, 10)
                    ]);
                    try {
                        $promo->addMediaFromUrl('https://loremflickr.com/800/600')->toMediaCollection('main');
                    } catch (\Exception $exception) {}
                }
            }
            if ($template == MainPageTemplates::products) {
                for ($i = 0; $i <= 10; $i++) {
                    MainPageProduct::create([
                        'main_page_block_id' => $item->id,
                        'product_id' => mt_rand(1, 80),
                        'title' => fake('ru_RU')->word(),
                    ]);
                }
            }
            if ($template == MainPageTemplates::articles) {
                for ($i = 0; $i <= 3; $i++) {
                    /** @var Article $article */
                    $article = Article::create([
                        'title' => fake('ru_ru')->realText(30),
                        'description' => fake('ru_ru')->realText(),
                    ]);
                    try {
                        $article->addMediaFromUrl('https://loremflickr.com/800/600')->toMediaCollection();
                    } catch (\Exception $exception) {}
                }
            }
        }
    }
}
