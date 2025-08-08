<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefAcquiringService;
use Illuminate\Http\Request;

class AcquiringController extends Controller
{
    public function __construct(protected ChiefAcquiringService $acquiringService)
    {
    }

    public function show()
    {
        $response = $this->acquiringService->show();

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->acquiringService->update($id, $request);

        return api_response($response->object(), $response->status());
    }
}
