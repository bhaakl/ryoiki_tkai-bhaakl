<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\ChangeRegistrationPhoneRequest;
use App\Http\Requests\v1\Dashboard\CompleteRequest;
use App\Http\Requests\v1\Dashboard\LoginRequest;
use App\Http\Requests\v1\Dashboard\RegistrationRequest;
use App\Http\Resources\v1\Dashboard\UserResource;
use App\Models\User;
use App\Services\AdminService;
use App\Services\SecurityService;
use App\Services\TenantServiceCreator;
use App\Traits\RespondWithTokenTrait;

class AuthController extends Controller
{
    use RespondWithTokenTrait;

    protected string $userResourceClass = UserResource::class;
    protected const GUARD = 'api';

    public function registration(RegistrationRequest $request, AdminService $adminService)
    {
        $user = $adminService->createChiefUser($request->validated());
        try {
            app(SecurityService::class, ['user' => $user])->sendCode();
        } catch (\Throwable $exception) {
            return api_error($exception->getMessage());
        }

        app(TenantServiceCreator::class)->new($user);

        return api_response();
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::email($credentials['email'])->whereNotNull('phone_verified_at')->first();

        if (!$user) {
            return api_error('Пользователь не найден', 404);
        }

        if (!$user->hasAnyRole($this->allowForRoles())) {
            return api_error('Not found', 404);
        }

        if (!auth('api')->validate($credentials)) {
            return api_error(['error' => 'Данные неверные'], 404);
        }

        try {
            app(SecurityService::class, ['user' => $user])->sendCode();
        } catch (\Throwable $exception) {
            return api_error($exception->getMessage());
        }

        return api_response(['message' => sprintf('Код отправлен на номер %s', $user->phone)]);
    }

    public function changePhone(ChangeRegistrationPhoneRequest $request)
    {
        $user = User::wherePhone($request->old_phone)->first();
        $user->update(['phone' => $request->phone]);
        app(SecurityService::class, ['user' => $user])->sendCodeToAnotherNumber($request->old_phone);

        return api_response(['message' => sprintf('Код отправлен на номер %s', $user->phone)]);
    }

    public function complete(CompleteRequest $request)
    {
        $params = $request->validated();
        $user = User::phone($params['phone'])->firstOrFail();
        if (!$user->hasAnyRole($this->allowForRoles())) {
            return api_error('Not found', 404);
        }

        try {
            app(SecurityService::class, ['user' => $user])->confirmPhone($params['code']);
        } catch (\Throwable $exception) {
            return api_error($exception->getMessage());
        }

        return $this->respondWithToken(auth()->login($user), self::GUARD);
    }

    public function logout()
    {
        auth('api')->invalidate(true);

        return api_response(['message' => 'Вы вышли']);
    }

    protected function allowForRoles(): array
    {
        return [Roles::CHIEF];
    }
}
