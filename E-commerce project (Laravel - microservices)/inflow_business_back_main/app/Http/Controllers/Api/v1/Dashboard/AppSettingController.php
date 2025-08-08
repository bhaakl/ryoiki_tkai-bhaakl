<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\AndroidMediaUploadRequest;
use App\Http\Requests\v1\Dashboard\AndroidSettingRequest;
use App\Http\Requests\v1\Dashboard\AuthTypeUpdateRequest;
use App\Http\Requests\v1\Dashboard\IosContactSettingRequest;
use App\Http\Requests\v1\Dashboard\IosMediaUploadRequest;
use App\Http\Requests\v1\Dashboard\IosSettingRequest;
use App\Http\Requests\v1\Dashboard\MainSettingsMediaUploadRequest;
use App\Http\Requests\v1\Dashboard\MainSettingsUpdateRequest;
use App\Http\Resources\v1\App\MediaResource;
use App\Http\Resources\v1\Dashboard\AppConfigResource;
use App\Models\AndroidSetting;
use App\Models\AppSetting;
use App\Models\IosSetting;
use App\Models\Media;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ApplicationService;

class AppSettingController extends Controller
{
    protected Tenant $tenant;
    protected AppSetting $appSetting;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
        $this->appSetting = AppSetting::first();
    }

    public function show()
    {
        return api_response(new AppConfigResource($this->tenant));
    }

    public function updateAuthType(AuthTypeUpdateRequest $request)
    {
        $this->appSetting->update($request->validated());

        return api_response(new AppConfigResource($this->tenant));
    }

    public function updateMainSettings(MainSettingsUpdateRequest $request)
    {
        app(ApplicationService::class, ['app' => $this->appSetting])->update($request->validated());

        return api_response(new AppConfigResource($this->tenant));
    }

    public function uploadMainMedia(MainSettingsMediaUploadRequest $request)
    {
        foreach (Media::MAIN_COLLECTIONS as $collection) {
            if ($request->file($collection)) {
                $this->appSetting->addMedia($request->file($collection))->toMediaCollection($collection);
            }
        }

        return api_response(new AppConfigResource($this->tenant));
    }

    public function updateIos(IosSettingRequest $request)
    {
        app(ApplicationService::class)->updateIosSettings($request->validated());

        return api_response(new AppConfigResource($this->tenant));
    }

    public function updateIosContacts(IosContactSettingRequest $request)
    {
        app(ApplicationService::class)->updateIosSettings($request->validated());

        return api_response(new AppConfigResource($this->tenant));
    }

    public function uploadIosMedia(IosMediaUploadRequest $request)
    {
        /** @var IosSetting $this->appSetting */
        $ios_setting = IosSetting::first();

        $uploaded = [];
        foreach (Media::IOS_COLLECTIONS as $collection) {
            if ($request->file($collection)) {
                $uploaded[] = $ios_setting->addMedia($request->file($collection))->toMediaCollection($collection);
            }
        }

        return api_response(MediaResource::collection($uploaded));
    }

    public function updateAndroid(AndroidSettingRequest $request)
    {
        app(ApplicationService::class)->updateAndroidSettings($request->validated());

        return api_response(new AppConfigResource($this->tenant));
    }

    public function uploadAndroidMedia(AndroidMediaUploadRequest $request)
    {
        /** @var AndroidSetting $this->appSetting */
        $android_setting = AndroidSetting::first();

        $uploaded = [];
        foreach (Media::ANDROID_COLLECTIONS as $collection) {
            if ($request->file($collection)) {
                $uploaded[] = $android_setting->addMedia($request->file($collection))->toMediaCollection($collection);
            }
        }

        return api_response(MediaResource::collection($uploaded));
    }
}
