<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Filters\StoreFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\StoreFormRequest;
use App\Http\Requests\v1\StoreUpdateRequest;
use App\Http\Resources\v1\App\Store\StoreCollection;
use App\Http\Resources\v1\App\Store\StoreResource;
use App\Models\DeliveryStore;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request, StoreFilter $filter)
    {
        $stores = Store::filter($filter)->paginate($request->per_page ?? 15);

        return new StoreCollection($stores);
    }

    public function store(StoreFormRequest $request)
    {
        $store = Store::create($request->validated());

        return new StoreResource($store);
    }

    public function show(string $id)
    {
        $store = Store::findOrFail($id);

        return new StoreResource($store);
    }

    public function update(StoreUpdateRequest $request, string $id)
    {
        /** @var Store $store */
        $store = Store::findOrFail($id);
        $store->update($request->validated());

        if (!$store->pickup) {
            DeliveryStore::whereStoreId($store->id)->delete();
        }

        return new StoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return response()->json(['message' => 'Ok']);
    }
}
