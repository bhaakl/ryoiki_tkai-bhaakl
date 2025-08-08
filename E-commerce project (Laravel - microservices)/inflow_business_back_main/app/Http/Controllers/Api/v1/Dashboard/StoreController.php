<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefStoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(private ChiefStoreService $storeService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->storeService->getList($request);

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->storeService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function show($id)
    {
        $response = $this->storeService->show($id);

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->storeService->update($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->storeService->delete($id);

        return api_response($response->object(), $response->status());
    }
}
