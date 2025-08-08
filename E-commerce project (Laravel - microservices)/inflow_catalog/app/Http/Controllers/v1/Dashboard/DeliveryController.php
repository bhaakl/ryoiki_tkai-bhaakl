<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Enums\DeliveryIcons;
use App\Enums\DeliveryTypes;
use App\Filters\DeliveryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\DeliveryConditionCreateRequest;
use App\Http\Requests\v1\DeliveryConditionUpdateRequest;
use App\Http\Requests\v1\DeliveryCreateRequest;
use App\Http\Requests\v1\DeliveryRestrictionCreateRequest;
use App\Http\Requests\v1\DeliveryUpdateRequest;
use App\Http\Requests\v1\IntervalRequest;
use App\Http\Resources\v1\App\Store\StoreResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryDetailResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryIconResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryIntervalResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryPriceConditionResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryRestrictionResource;
use App\Http\Resources\v1\Dashboard\Delivery\DeliveryTypeResource;
use App\Models\Delivery;
use App\Models\DeliveryInterval;
use App\Models\DeliveryPriceCondition;
use App\Models\DeliveryRestriction;
use App\Models\DeliveryRestrictionDeliveryInterval;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function getDeliveryTypes()
    {
        $types = DeliveryTypes::cases();

        return DeliveryTypeResource::collection($types);
    }

    public function getDeliveryIcons()
    {
        $icons = DeliveryIcons::cases();

        return DeliveryIconResource::collection($icons);
    }

    public function index(DeliveryFilter $filter)
    {
        $delivery = Delivery::filter($filter)->get();

        return DeliveryResource::collection($delivery);
    }

    public function store(DeliveryCreateRequest $request)
    {
        $delivery = Delivery::create($request->validated());

        return new DeliveryDetailResource($delivery);
    }

    public function show($delivery)
    {
        $delivery = Delivery::findOrFail($delivery);

        return new DeliveryDetailResource($delivery);
    }

    public function update($delivery, DeliveryUpdateRequest $request)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::find($delivery);
        $delivery->update($request->validated());
        if ($delivery->type != DeliveryTypes::PICKUP->value) {
            $delivery->stores()->detach();
        }

        return new DeliveryDetailResource($delivery->refresh());
    }

    public function destroy($delivery)
    {
        $delivery = Delivery::findOrFail($delivery);
        if (Order::whereStatusId($delivery)->orWhere('prev_status_id', $delivery)->exists()) {
            return response()->json(['message' => 'Способ доставки используется. Невозможно удалить'], 400);
        }
        $delivery->delete();

        return response()->json(['message' => 'ok']);
    }

    public function getAvailableStores($delivery)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($delivery);
        if ($delivery->type != DeliveryTypes::PICKUP->value) {
            return response()->json('Неверный способ доставки', 400);
        }
        $stores = Store::wherePickup(true)->whereNotIn('id', $delivery->stores->pluck('id'))->get();

        return StoreResource::collection($stores);
    }

    public function attachStore($delivery, $store)
    {
        $delivery = Delivery::findOrFail($delivery);
        if ($delivery->type != DeliveryTypes::PICKUP->value) {
            return response()->json('Неверный способ доставки', 400);
        }
        $store = Store::findOrFail($store);
        if (!$store->pickup) {
            return response()->json('Салон указан неверно', 400);
        }
        if (!$delivery->stores->contains($store)) {
            $delivery->stores()->attach($store);
        }

        return new DeliveryDetailResource($delivery->refresh());
    }

    public function detachStore($delivery, $store)
    {
        $delivery = Delivery::findOrFail($delivery);
        if ($delivery->type != DeliveryTypes::PICKUP->value) {
            return response()->json('Неверный способ доставки', 400);
        }
        /** @var Store $store */
        $store = Store::findOrFail($store);
        $delivery->stores()->detach($store);

        return new DeliveryDetailResource($delivery->refresh());
    }

    public function attachInterval($delivery, IntervalRequest $request)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($delivery);

        /** @var DeliveryInterval $interval */
        $interval = DeliveryInterval::create([
            'delivery_id' => $delivery->id,
            'time_from' => $request->time_from,
            'time_to' => $request->time_to,
            'interval' => $request->time_from . '-' . $request->time_to
        ]);

        return new DeliveryIntervalResource($interval);
    }

    public function detachInterval($delivery, $interval)
    {
        /** @var DeliveryInterval $interval */
        $interval = DeliveryInterval::whereDeliveryId($delivery)->findOrFail($interval);
        $interval->delete();

        return response()->json(['message' => 'ok']);
    }

    public function attachCondition($delivery, DeliveryConditionCreateRequest $request)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($delivery);

        /** @var DeliveryPriceCondition $condition */
        $condition = DeliveryPriceCondition::create([
            'delivery_id' => $delivery->id,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'price' => $request->price,
        ]);

        return new DeliveryPriceConditionResource($condition);
    }

    public function updateCondition($delivery, $condition, DeliveryConditionUpdateRequest $request)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($delivery);

        /** @var DeliveryPriceCondition $condition */
        $condition = DeliveryPriceCondition::whereDeliveryId($delivery->id)->findOrFail($condition);
        $condition->update($request->validated());

        return new DeliveryPriceConditionResource($condition);
    }

    public function detachCondition($delivery, $condition)
    {
        /** @var DeliveryPriceCondition $condition */
        $condition = DeliveryPriceCondition::whereDeliveryId($delivery)->findOrFail($condition);
        $condition->delete();

        return response()->json(['message' => 'ok']);
    }

    public function attachRestriction($delivery, DeliveryRestrictionCreateRequest $request)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($delivery);

        /** @var DeliveryRestriction $restriction */
        $restriction = DeliveryRestriction::create([
            'delivery_id' => $delivery->id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ]);

        if ($request->intervals && $delivery->has_intervals) {
            $restriction->intervals()->sync($request->intervals);
        }

        return new DeliveryRestrictionResource($restriction);
    }

    public function updateRestriction($delivery, $restriction, DeliveryRestrictionCreateRequest $request)
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::findOrFail($delivery);

        /** @var DeliveryRestriction $restriction */
        $restriction = DeliveryRestriction::whereDeliveryId($delivery->id)->findOrFail($restriction);
        $restriction->update($request->validated());

        if ($request->intervals && $delivery->has_intervals) {
            $restriction->intervals()->sync($request->intervals);
        }

        return new DeliveryRestrictionResource($restriction);
    }

    public function detachRestriction($delivery, $restriction)
    {
        /** @var DeliveryRestriction $restriction */
        $restriction = DeliveryRestriction::whereDeliveryId($delivery)->findOrFail($restriction);
        $restriction->delete();

        return response()->json(['message' => 'ok']);
    }
}
