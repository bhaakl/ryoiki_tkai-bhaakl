<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductFilter extends QueryFilter
{
    public function city(?int $city = null)
    {
        if (!$city)
            return;

        $this->builder->whereCityId($city);
    }

    public function category($category)
    {
        $this->builder->whereHas('parent', function ($subQuery) use ($category) {
            $subQuery->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category);
            });
        });
    }

    public function title(string $title)
    {
        $words = array_filter(explode(' ', $title));

        $this->builder->where(function (Builder $query) use ($words) {
            foreach ($words as $word) {
                $query->where('title', 'like', "%$word%");
            }
        });
    }

    public function active(?string $val = null)
    {
        if ($val == null)
            return;

        $val = $val === "true";

        $this->builder->where('active', $val);
    }

    public function by_order(?string $val = 'id')
    {
        if ($val == null)
            return;

        $val = $val === "true";

        $this->builder->where('by_order', $val);
    }

    public function price_from(string $price_from)
    {
        $this->builder->where(function ($query) use ($price_from) {
            $query->where(DB::raw('COALESCE(NULLIF(promo_price, 0), price)'), '>=', $price_from);
        });
    }

    public function price_to(string $price_to)
    {
        $this->builder->where(function ($query) use ($price_to) {
            $query->where(DB::raw('COALESCE(NULLIF(promo_price, 0), price)'), '<=', $price_to);
        });
    }

    public function some($value = null)
    {

    }

    public function sort($val = null)
    {
        if (!$val || !Str::contains($val, ','))
            return;

        $sort = explode(',', $val);

        if (!in_array($sort[0], ['price', 'created_at']))
            return;

        if (!in_array($sort[1], ['asc', 'desc']))
            return;

        if ($sort[0] == 'price') {
            $this->builder->orderByRaw('COALESCE(NULLIF(promo_price, 0), price) ' . $sort[1]);
        } else {
            $this->builder->orderBy($sort[0], $sort[1]);
        }
    }
}
