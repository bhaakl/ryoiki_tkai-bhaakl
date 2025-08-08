<?php

namespace App\Http\Controllers\v1\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\KitProductsRequest;
use App\Http\Resources\v1\App\Kit\KitCollection;
use App\Models\Kit;

class KitController extends Controller
{
    public function index(KitProductsRequest $request)
    {
        $kits = Kit::query();
        if ($request->product) {
            $kits = $kits->whereHas('items', function ($query) use ($request) {
                $query->whereProductId($request->product);
            });
        }
        $kits = $kits->paginate($request->per_page ?? self::PER_PAGE);;

        return new KitCollection($kits);
    }
}
