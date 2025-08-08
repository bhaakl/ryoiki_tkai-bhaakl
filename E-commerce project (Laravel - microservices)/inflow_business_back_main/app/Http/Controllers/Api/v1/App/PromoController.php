<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\App\PromoResource;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function show(Promo $promo)
    {
        return api_response(new PromoResource($promo));
    }
}
