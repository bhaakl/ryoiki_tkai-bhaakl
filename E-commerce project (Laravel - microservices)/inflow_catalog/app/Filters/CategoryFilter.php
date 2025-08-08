<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryFilter extends QueryFilter
{
    public function city(?int $city = null)
    {
        if (!$city)
            return;

        $this->builder->whereCityId($city);
    }

    public function active(int $val = 0)
    {
        $this->builder->where('active', $val);
    }

    public function parent_id(?int $parent = null)
    {
        $this->builder->where('categories.parent_id', $parent);
    }

    public function sort($val = null)
    {
        if (!$val || !Str::contains($val, ','))
            return;

        $sort = explode(',', $val);

        if (!in_array($sort[1], ['asc', 'desc']))
            return;

        if ($sort[0] == 'parent') {
            $this->builder->join('categories as p', 'p.id', '=', 'categories.parent_id')
                ->orderBy('p.name', $sort[1])
                ->select('categories.id', 'categories.name', 'categories.parent_id', 'categories.active', 'categories.updated_at',);
        } else {
            $this->builder->orderBy($sort[0], $sort[1]);
        }
    }
}
