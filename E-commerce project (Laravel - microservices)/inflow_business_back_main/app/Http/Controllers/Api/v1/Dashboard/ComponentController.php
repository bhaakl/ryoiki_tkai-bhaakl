<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefComponentService;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function __construct(protected ChiefComponentService $componentService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->componentService->index($request);

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->componentService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function units()
    {
        $response = $this->componentService->units();

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->componentService->update($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->componentService->delete($id);

        return api_response($response->object(), $response->status());
    }
}
