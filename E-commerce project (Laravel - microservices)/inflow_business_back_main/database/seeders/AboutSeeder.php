<?php

namespace Database\Seeders;

use App\Enums\AboutTemplates;
use App\Models\About;
use App\Models\AboutItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var About $about */
        $about = About::firstOrCreate();
        try {
            $about->addMediaFromUrl('https://loremflickr.com/600/600')->toMediaCollection();
        } catch (\Exception $exception) {}

        foreach (AboutTemplates::cases() as $template) {

            $item = new AboutItem();
            $item->type = $template;
            $item->title = fake()->word;
            if ($template == AboutTemplates::html_text) {
                $item->text = fake('ru_RU')->realText;
            }
            $item->save();
            if ($template == AboutTemplates::photo) {
                try {
                    $item->addMediaFromUrl('https://loremflickr.com/600/600')->toMediaCollection();
                } catch (\Exception $exception) {}
            }
            if ($template == AboutTemplates::license) {
                try {
                    $item->addMediaFromUrl('https://loremflickr.com/600/600')
                        ->withCustomProperties([
                            'name' => fake('ru_RU')->word,
                            'description' => fake('ru_RU')->realText
                        ])
                        ->toMediaCollection();
                } catch (\Exception $exception) {}
            }
        }
    }
}
