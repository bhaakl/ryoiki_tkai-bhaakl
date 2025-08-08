<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class DeliveryFilter extends QueryFilter
{
    public function city(?int $city = null)
    {
        if (!$city)
            return;

        $this->builder->whereCityId($city);
    }

    public function type(string $value)
    {
        $this->builder->where('type', $value);
    }
}
