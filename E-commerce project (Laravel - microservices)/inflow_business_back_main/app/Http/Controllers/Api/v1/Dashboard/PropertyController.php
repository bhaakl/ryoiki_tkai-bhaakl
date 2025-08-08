<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefPropertyService;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __construct(protected ChiefPropertyService $propertyService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->propertyService->index($request);

        return api_response($response->object(), $response->status());
    }

    public function dropout()
    {
        $response = $this->propertyService->dropout();

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->propertyService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function show($id)
    {
        $response = $this->propertyService->show($id);

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->propertyService->update($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->propertyService->delete($id);

        return api_response($response->object(), $response->status());
    }

    public function getEnums($id)
    {
        $response = $this->propertyService->getEnums($id);

        return api_response($response->object(), $response->status());
    }

    public function addEnum(Request $request)
    {
        $response = $this->propertyService->addEnum($request);

        return api_response($response->object(), $response->status());
    }

    public function destroyEnum($id)
    {
        $response = $this->propertyService->deleteEnum($id);

        return api_response($response->object(), $response->status());
    }

    public function getStrings(Request $request)
    {
        $response = $this->propertyService->getStrings($request);

        return api_response($response->object(), $response->status());
    }
}
