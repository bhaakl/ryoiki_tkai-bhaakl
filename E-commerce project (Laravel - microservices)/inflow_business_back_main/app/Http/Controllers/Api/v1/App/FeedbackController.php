<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Requests\v1\App\FeedbackRequest;

class FeedbackController extends AppController
{
    public function __invoke(FeedbackRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    {
        return api_response();
    }
}
