<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\Roles;
use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\PasswordResetRequest;
use App\Http\Requests\v1\Dashboard\SendPasswordResetLinkRequest;
use App\Models\User;
use App\Services\SecurityService;

class PasswordResetController extends Controller
{
    public function sendLink(SendPasswordResetLinkRequest $request)
    {
        $user = User::whereEmail($request->get('email'))->firstOrFail();
        if (!$user->hasAnyRole($this->allowForRoles())) {
            return api_error('Not found', 404);
        }

        try {
            app(SecurityService::class)->sendResetLink($user);
        } catch (ServiceException $e) {
            return api_error($e->getMessage());
        } catch (\Throwable $e) {
            return api_error('internal error');
        }

        return api_response();
    }

    public function reset(PasswordResetRequest $request)
    {
        $params = $request->validated();
        try {
            app(SecurityService::class)->resetPassword(
                $params['password'],
                $request->query->get('token'),
            );
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
