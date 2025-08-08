<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefCategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected ChiefCategoryService $categoryService)
    {}

    public function index(Request $request)
    {
        $response = $this->categoryService->index($request);

        return api_response($response->object(), $response->status());
    }

    public function dropout(Request $request)
    {
        $response = $this->categoryService->dropout($request);

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->categoryService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function show($id)
    {
        $response = $this->categoryService->show($id);

        return api_response($response->object(), $response->status());
    }

    public function update($id, Request $request)
    {
        $response = $this->categoryService->update($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function uploadImage($id, Request $request)
    {
        $response = $this->categoryService->uploadImage($id, $request);

        return api_response($response->object(), $response->status());
    }

    public function deleteImage($id)
    {
        $response = $this->categoryService->deleteImage($id);

        return api_response($response->object(), $response->status());
    }

    public function delete($id)
    {
        $response = $this->categoryService->delete($id);

        return api_response($response->object(), $response->status());
    }
}
