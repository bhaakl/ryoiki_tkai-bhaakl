<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class OfferFilter extends QueryFilter
{
    public function sort($val = null)
    {
        if (!$val || !Str::contains($val, ','))
            return;

        $sort = explode(',', $val);

        if (!in_array($sort[1], ['asc', 'desc']))
            return;

        $this->builder->orderBy($sort[0], $sort[1]);
    }
}
