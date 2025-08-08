<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\SocialNetworks;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\ContactInfoUpdateRequest;
use App\Http\Resources\v1\Dashboard\ContactInfoResource;
use App\Models\AppSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ContactInfoService;

class ContactInfoController extends Controller
{
    protected Tenant $tenant;

    protected AppSetting $appSetting;

    public function __construct(
        private readonly ContactInfoService $service
    ) {
        /** @var User $user */
        $user = $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
        $this->appSetting = AppSetting::first();
    }

    public function index()
    {
        return api_response(new ContactInfoResource($this->tenant));
    }

    public function update(ContactInfoUpdateRequest $request)
    {
        $tenant = $this->service->updateContactInfo($request->validated());

        return api_response(new ContactInfoResource($tenant));
    }

    public function getAvailableSocialNetworks()
    {
        return response()->api(SocialNetworks::getAll());
    }
}
