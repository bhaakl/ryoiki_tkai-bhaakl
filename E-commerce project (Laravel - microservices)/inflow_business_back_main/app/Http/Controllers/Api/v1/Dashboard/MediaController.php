<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\FileUploadRequest;
use App\Http\Requests\v1\Dashboard\MediaDeleteRequest;
use App\Http\Resources\v1\App\MediaResource;
use App\Models\About;
use App\Models\AppSetting;
use App\Models\Media;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MediaService;
use App\Traits\ImageTrait;

class MediaController extends Controller
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
        About::firstOrCreate();
    }

    public function upload(FileUploadRequest $request)
    {
        $media = app(MediaService::class, ['app' => AppSetting::query()->first()])
            ->saveFile($request->file('file'), $request->get('collection'));

        return api_response(new MediaResource($media));
    }

    public function delete(MediaDeleteRequest $request)
    {
        Media::find($request->input('id'))->delete();

        return api_response(['message' => 'Media deleted']);
    }
}
