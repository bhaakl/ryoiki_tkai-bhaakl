<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ChiefTagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(protected ChiefTagService $tagService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->tagService->index($request);

        return api_response($response->object(), $response->status());
    }

    public function store(Request $request)
    {
        $response = $this->tagService->create($request);

        return api_response($response->object(), $response->status());
    }

    public function destroy($id)
    {
        $response = $this->tagService->delete($id);

        return api_response($response->object(), $response->status());
    }
}
