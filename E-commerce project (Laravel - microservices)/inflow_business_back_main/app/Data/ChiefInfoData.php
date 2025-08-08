<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ChiefInfoData extends Data
{
    public function __construct(
        public string $phone,
        public string $email,
        public ?array $social_links
    ) {
    }
}
