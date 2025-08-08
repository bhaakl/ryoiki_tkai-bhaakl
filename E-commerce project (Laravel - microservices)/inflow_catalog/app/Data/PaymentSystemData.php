<?php

namespace App\Data;

class PaymentSystemData
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public string $description,
        public string $icon,
    )
    {
    }

    public static function fromData($data): self
    {
        return new self(
            id: $data->id,
            type: $data->type,
            name: $data->name,
            description: $data->description,
            icon: $data->icon,
        );
    }
}
