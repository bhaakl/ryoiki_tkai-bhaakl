<?php

namespace App\Traits;

use Tymon\JWTAuth\Facades\JWTFactory;

trait RespondWithTokenTrait
{
    protected function respondWithToken($token, string $guard)
    {
        return api_response([
            'token' => [
                'access_token' => $token,
                'refresh_token' => $token,
            ],
            'token_type' => 'bearer',
            'expires_in' => auth($guard)->factory(JWTFactory::class)->getTTL() * 60,
            'user' => new $this->userResourceClass(auth($guard)->user()),
        ]);
    }
}
