<?php

namespace App\Http\Controllers\v1\App;

use App\Filters\DeliveryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\DeliveryCalendarRequest;
use App\Http\Resources\v1\App\Delivery\DeliveryIntervalResource;
use App\Http\Resources\v1\App\Delivery\DeliveryResource;
use App\Http\Resources\v1\App\Store\StoreResource;
use App\Models\Delivery;
use App\Models\DeliveryRestriction;
use App\Models\ProductRestriction;

class DeliveryController extends Controller
{
    public function index(DeliveryFilter $filter)
    {
        $delivery = Delivery::filter($filter)->get();

        return DeliveryResource::collection($delivery);
    }

    public function getStores(Delivery $delivery)
    {
        return StoreResource::collection($delivery->stores()->where('stores.pickup', true)->get());
    }

    public function getCalendar(Delivery $delivery, DeliveryCalendarRequest $request)
    {
        $dates = [];
        for ($i = 0; $i < 14; $i++) {
            $date = now()->addDays($i)->toDateString();
            $dates_item = new \stdClass();
            $dates_item->date = $date;
            $dates_item->allowed = true;
            $dates_item->intervals = collect();
            $delivery_restriction = DeliveryRestriction::whereDate('date_from', '>=', $date)->whereDate('date_to', '<=', $date)->first();
            if (!$delivery_restriction && $delivery->has_intervals) {
                $dates_item->intervals = $delivery->intervals;
            } elseif ($delivery_restriction && !$delivery->has_intervals) {
                $dates_item->allowed = false;
            } elseif ($delivery_restriction && $delivery->has_intervals) {
                $allowed_intervals = $delivery->intervals->diff($delivery_restriction->intervals);
                $dates_item->intervals = $allowed_intervals;
            }

            if ($request->products) {
                $product_restrictions = ProductRestriction::where('date', $date)
                    ->whereIn('product_id', $request->products)
                    ->where(function ($query) use ($delivery) {
                        $query->whereTotal(true)->orWhereHas('deliveries', function ($subquery) use ($delivery) {
                            $subquery->whereDeliveryId($delivery->id);
                        });
                    })
                    ->get();
                if ($product_restrictions->count()) {
                    foreach ($product_restrictions as $product_restriction) {
                        if ($product_restriction->total) {
                            $dates_item->intervals = collect();
                            $dates_item->allowed = false;
                            break;
                        } elseif ($dates_item->intervals) {
                            $intervals = collect();
                            $delivery_product_restrictions = $product_restriction->deliveries()->whereDeliveryId($delivery->id)->get();
                            foreach ($delivery_product_restrictions as $delivery_product_restriction) {
                                if ($delivery_product_restriction->all_day) {
                                    $dates_item->intervals = collect();
                                    break;
                                } else {
                                    $intervals->merge($delivery_product_restriction->intervals);
                                }
                            }
                            $dates_item->intervals = $dates_item->intervals->diff($intervals);
                        }
                    }
                }
            }

            if ($dates_item->intervals->count() > 0) {
                $dates_item->intervals = DeliveryIntervalResource::collection($dates_item->intervals);
            } elseif ($dates_item->intervals->count() == 0 && $delivery->has_intervals) {
                unset($dates_item->intervals);
                $dates_item->allowed = false;
            } elseif (!$delivery->has_intervals) {
                unset($dates_item->intervals);
            }

            $dates[] = $dates_item;
        }

        return $dates;
    }
}
