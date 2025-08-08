<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\App\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function __invoke()
    {
        return api_response(BannerResource::collection(Banner::active()->get()));
    }
}
