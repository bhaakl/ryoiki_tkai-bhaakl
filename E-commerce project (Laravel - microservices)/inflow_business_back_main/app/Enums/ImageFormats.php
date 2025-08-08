<?php

namespace App\Enums;

enum ImageFormats: string
{
    case JPG = "jpeg";
    case PNG = "png";

    public function extention(): string {
        return match($this) {
            ImageFormats::JPG => '.jpg',
            ImageFormats::PNG => '.png',
        };
    }

    public function method(): string {
        return match($this) {
            ImageFormats::JPG => 'toJpg',
            ImageFormats::PNG => 'toPng',
        };
    }
}
