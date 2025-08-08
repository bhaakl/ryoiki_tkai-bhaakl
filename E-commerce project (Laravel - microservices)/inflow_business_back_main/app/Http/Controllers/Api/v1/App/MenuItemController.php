<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Resources\v1\App\MenuItemContentResource;
use App\Models\MenuItem;

class MenuItemController extends AppController
{
    public function __invoke(MenuItem $menuItem)
    {
        return api_response(new MenuItemContentResource($menuItem));
    }
}
