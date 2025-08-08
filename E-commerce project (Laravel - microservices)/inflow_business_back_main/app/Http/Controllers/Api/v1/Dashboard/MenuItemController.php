<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\MenuKeyIcons;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\MenuItemCreateRequest;
use App\Http\Requests\v1\Dashboard\MenuItemUpdateRequest;
use App\Http\Resources\v1\Dashboard\MenuItemDetailResource;
use App\Http\Resources\v1\Dashboard\MenuItemIconResource;
use App\Http\Resources\v1\Dashboard\MenuItemResource;
use App\Models\MenuItem;
use App\Models\Tenant;
use App\Models\User;

class MenuItemController extends Controller
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

    public function icons()
    {
        $icons = MenuKeyIcons::cases();

        return api_response(MenuItemIconResource::collection($icons));
    }

    public function index()
    {
        $items = MenuItem::orderBy('position')->get();

        return api_response(MenuItemResource::collection($items));
    }

    public function store(MenuItemCreateRequest $request)
    {
        $item = MenuItem::create($request->validated());

        return api_response(MenuItemDetailResource::make($item->refresh()));
    }

    public function show($item)
    {
        $item = MenuItem::findOrFail($item);

        return api_response(MenuItemDetailResource::make($item));
    }

    public function update(MenuItemUpdateRequest $request, $item)
    {
        $item = MenuItem::findOrFail($item);
        $item->update($request->validated());

        return api_response(MenuItemDetailResource::make($item->refresh()));
    }

    public function destroy($item)
    {
        $item = MenuItem::findOrFail($item);
        if (!$item->value->isCustom()) {
            return api_error('Нельзя удалить системный пункт меню', 400);
        }

        $item->delete();

        return api_response(['message' => 'ok']);
    }
}
