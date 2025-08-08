<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Resources\v1\App\AboutItemResource;
use App\Http\Resources\v1\App\AboutResource;
use App\Models\About;
use App\Models\AboutItem;

class AboutController extends AppController
{
    public function index()
    {
        $about = About::firstOrCreate();

        return api_response(new AboutResource($about));
    }

    public function showItem(AboutItem $aboutItem)
    {
        return api_response(new AboutItemResource($aboutItem));
    }
}
