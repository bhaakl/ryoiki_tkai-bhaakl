<?php

namespace App\Http\Controllers\Api\v1\Dashboard\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Api\v1\Dashboard\AuthController as ChiefAuthController;
use App\Http\Requests\v1\Dashboard\RegistrationRequest;
use App\Http\Resources\v1\Dashboard\UserResource;
use App\Services\AdminService;
use App\Services\SecurityService;
use App\Traits\RespondWithTokenTrait;

class AuthController extends ChiefAuthController
{
    use RespondWithTokenTrait;

    protected string $userResourceClass = UserResource::class;
    protected const GUARD = 'api';

    public function registration(RegistrationRequest $request)
    {
        $user = app(AdminService::class)->createSuperAdminUser($request->validated());
        app(SecurityService::class, ['user' => $user])->sendCode();

        return api_response();
    }

    protected function allowForRoles(): array
    {
        return [Roles::SUPER_ADMIN];
    }
}
