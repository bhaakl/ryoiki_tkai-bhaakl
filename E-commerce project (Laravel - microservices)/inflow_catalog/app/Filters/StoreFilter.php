<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreFilter extends QueryFilter
{
    public function active(int $val = 0)
    {
        $this->builder->where('active', $val);
    }

    public function pickup(int $val = 0)
    {
        $this->builder->where('pickup', $val);
    }

    public function shop(int $val = 0)
    {
        $this->builder->where('shop', $val);
    }

    public function search(string $search)
    {
        $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%');
        });
    }

    public function sort($val = null)
    {
        if (!$val || !Str::contains($val, ','))
            return;

        $sort = explode(',', $val);

        if (!in_array($sort[0], ['name', 'address', 'subway', 'active', 'pickup', 'shop']))
            return;

        if (!in_array($sort[1], ['asc', 'desc']))
            return;

        $this->builder->orderBy($sort[0], $sort[1]);
    }
}
