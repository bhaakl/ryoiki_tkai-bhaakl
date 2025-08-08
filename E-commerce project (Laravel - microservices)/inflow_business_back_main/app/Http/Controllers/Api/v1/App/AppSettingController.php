<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Resources\v1\App\AppConfigResource;
use App\Models\AppSetting;

class AppSettingController extends AppController
{
    public function getConfig()
    {
        $tenant = app('currentTenant');

        return api_response(new AppConfigResource($tenant));
    }

    public function getConfigDate()
    {
        /** @var AppSetting $settings */
        $settings = AppSetting::first();

        return api_response(['updated_at' => $settings->updated_at->timestamp]);
    }
}
