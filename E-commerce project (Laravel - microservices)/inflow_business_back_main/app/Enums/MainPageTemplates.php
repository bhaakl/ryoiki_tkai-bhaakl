<?php


namespace App\Enums;
use App\Models\MainPageBlock;
use Illuminate\Validation\Rule;

enum MainPageTemplates
{
    case promo;
    case products;
    case banners;
    case articles;


    public function toString(): string
    {
        return match ($this) {
            self::promo => 'Акции',
            self::products => 'Товарная выборка',
            self::banners => 'Рекламные баннеры',
            self::articles => 'Новости',
        };
    }

    public function addable(): bool
    {
        $articles_block = !MainPageBlock::whereTemplate(self::articles)->exists();

        return match($this) {
            self::banners => false,
            self::articles => $articles_block,
            default => true
        };
    }

    public function validation(): array
    {
        return match($this) {
            self::promo => [
                'title' => ['string', 'required'],
                'main_image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png'],
                'slider_images' => ['nullable','array'],
                'slider_images.*' => ['file', 'image', 'mimes:jpg,jpeg,png'],
                'description' => ['required', 'string'],
                'link' => ['nullable', 'url'],
            ],
            self::products => [
                'products' => ['required', 'array'],
                'products.*' => [Rule::exists('tenant.products', 'id')],
            ],
            self::banners => [
                'title' => ['string', 'required'],
                'category_id' => ['nullable', 'integer', 'gt:0'],
                'is_active' => ['required', 'boolean'],
                'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png'],
            ],
            self::articles => []
        };
    }
}
