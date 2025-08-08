<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersRequest;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Вернуть пользователя.
     */
    public function index(Request $request): ResourceCollection
    {
        return UserResource::collection(
            User::withCount(['posts'])->with('roles')->latest()->paginate($request->input('limit', 20))
        );
    }

    /**
     * Вернуть указанный ресурс.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Обновление указанного ресурса в хранилище.
     */
    public function update(UsersRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        if ($request->filled('password')) {
            $request->merge([
                'password' => Hash::make($request->input('password'))
            ]);
        }

        $user->update(array_filter($request->only(['name', 'email', 'password'])));

        return new UserResource($user);
    }
}
