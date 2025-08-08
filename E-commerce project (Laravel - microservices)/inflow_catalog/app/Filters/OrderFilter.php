<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OrderFilter extends QueryFilter
{
    public function city(?int $city = null)
    {
        if (!$city)
            return;

        $this->builder->whereCityId($city);
    }

    public function state($value = null)
    {
        if ($value == 'active') {
            $this->builder->active();
        } elseif ($value == 'inactive') {
            $this->builder->inactive();
        }
    }

    public function status($value = null)
    {
        if (!$value) return;

        $values = explode(',', $value);

        $this->builder->whereIn('status_id', $values);
    }

    public function user($value = null)
    {
        if (!$value) return;

        $this->builder->where('user_id', $value);
    }

    public function min_total($value = null)
    {
        if (!$value) return;

        $this->builder->where('total', '>=', $value);
    }

    public function max_total($value = null)
    {
        if (!$value) return;

        $this->builder->where('total', '<=', $value);
    }

    public function date_from($value = null)
    {
        if (!$value || !Carbon::parse($value)) return;

        $this->builder->whereDate('created_at', '>=', Carbon::parse($value));
    }

    public function date_to($value = null)
    {
        if (!$value || !Carbon::parse($value)) return;

        $this->builder->whereDate('created_at', '<=', Carbon::parse($value));
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
