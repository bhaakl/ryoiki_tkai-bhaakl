<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CustomerFilter extends QueryFilter
{
    public function search($val = null)
    {
        if (!$val)
            return;

        $this->builder->where(function ($query) use ($val) {
            $query->where('name', 'LIKE', "%$val%")
                ->orWhere('email', 'LIKE', "%$val%")
                ->orWhere('phone', 'LIKE', "%$val%");
        });
    }

    public function name($val = null)
    {
        if (!$val)
            return;

        $this->builder->where('name', 'like', "%$val%");
    }

    public function email($val = null)
    {
        if (!$val)
            return;

        $this->builder->where('email', 'like', "%$val%");
    }

    public function phone($val = null)
    {
        if (!$val)
            return;

        $this->builder->where('phone', 'like', "%$val%");
    }

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
