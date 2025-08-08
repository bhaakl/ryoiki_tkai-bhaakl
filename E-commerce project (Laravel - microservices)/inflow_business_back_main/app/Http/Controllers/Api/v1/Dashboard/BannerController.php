<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\BannerCreateRequest;
use App\Http\Requests\v1\Dashboard\BannerUpdateRequest;
use App\Http\Requests\v1\Dashboard\BannerUploadImageRequest;
use App\Http\Resources\v1\Dashboard\BannerResource;
use App\Models\Banner;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ImageTrait;

    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function index()
    {
        $banners = Banner::all();

        return api_response(BannerResource::collection($banners));
    }

    public function store(BannerCreateRequest $request)
    {
        $banner = Banner::create($request->validated());
        $this->uploadPhoto($request->file('image'), $banner);

        return api_response(new BannerResource($banner));
    }

    public function update($banner, BannerUpdateRequest $request)
    {
        /** @var Banner $banner */
        $banner = Banner::findOrFail($banner);

        $banner->update($request->validated());

        return api_response(new BannerResource($banner));
    }

    public function updateImage($banner, BannerUploadImageRequest $request)
    {
        /** @var Banner $banner */
        $banner = Banner::findOrFail($banner);

        $this->uploadPhoto($request->file('image'), $banner);

        return api_response(new BannerResource($banner));
    }

    public function destroy($banner)
    {
        /** @var Banner $banner */
        $banner = Banner::findOrFail($banner);

        $banner->delete();

        return api_response(['message' => 'ok']);
    }
}
