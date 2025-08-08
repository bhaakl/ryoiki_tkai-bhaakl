<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\ProfileUpdateRequest;
use App\Http\Resources\v1\Dashboard\UserResource;

class ProfileController extends Controller
{
    public function me()
    {
        return api_response(new UserResource(auth()->user()));
    }

    public function update(ProfileUpdateRequest $request)
    {

    }
}
