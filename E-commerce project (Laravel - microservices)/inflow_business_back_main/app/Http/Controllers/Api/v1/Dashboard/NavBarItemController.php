<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\NavBarItemUpdateRequest;
use App\Http\Resources\v1\Dashboard\NavBarItemResource;
use App\Models\NavBarItem;
use App\Models\Tenant;
use App\Models\User;

class NavBarItemController extends Controller
{
    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function index()
    {
        $items = NavBarItem::orderBy('position')->get();

        return api_response(NavBarItemResource::collection($items));
    }

    public function update($item, NavBarItemUpdateRequest $request)
    {
        /** @var NavBarItem $item */
        $item = NavBarItem::findOrFail($item);
        if ($request->has('position')) {
            $item->update(['position' => $request->position]);
        }
        if ($request->has('active') && $item->switchable) {
            $item->update(['active' => $request->active]);
        }

        return api_response(new NavBarItemResource($item));
    }
}
