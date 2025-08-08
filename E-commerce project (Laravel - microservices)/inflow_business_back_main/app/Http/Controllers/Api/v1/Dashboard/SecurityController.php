<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\SendCodeRequest;
use App\Models\User;
use App\Services\SecurityService;

class SecurityController extends Controller
{
    public function sendCode(SendCodeRequest $request)
    {
        $user = User::phone($request->validated()['phone'])->firstOrFail();
        if (!$user->hasAnyRole($this->allowForRoles())) {
            return api_error('Not found', 404);
        }

        try {
            app(SecurityService::class, ['user'=> $user])->sendCode();
        } catch (\Throwable $exception) {
            return api_error($exception->getMessage());
        }

        return api_response();
    }

    protected function allowForRoles(): array
    {
        return [Roles::CHIEF];
    }
}
