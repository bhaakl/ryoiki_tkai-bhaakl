<?php

namespace App\Services;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    private ?User $user;


    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    public function createChiefUser(array $params): User
    {
        DB::beginTransaction();
        $this->user = User::query()->create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
            'phone' => $params['phone'],
        ]);
        $this->user->assignRole(Roles::CHIEF);
        DB::commit();

        return $this->user;
    }

    public function createSuperAdminUser(array $params): User
    {
        DB::beginTransaction();
        $this->user = User::query()->create([
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
            'phone' => $params['phone'],
        ]);
        $this->user->assignRole(Roles::SUPER_ADMIN);
        DB::commit();

        return $this->user;
    }
}
