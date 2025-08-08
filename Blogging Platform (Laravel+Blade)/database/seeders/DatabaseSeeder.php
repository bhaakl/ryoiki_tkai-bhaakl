<?php

use App\Models\MediaLibrary;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles
        Role::firstOrCreate(['name' => Role::ROLE_USER]);
        $role_admin = Role::firstOrCreate(['name' => Role::ROLE_ADMIN]);
        $role_user = Role::firstOrCreate(['name' => Role::ROLE_USER]);

        // MediaLibrary
        MediaLibrary::firstOrCreate([]);

        // Users
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('adminpass'),
                'email_verified_at' => now()
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'user',
                'password' => Hash::make('userpass'),
                'email_verified_at' => now()
            ]
        );

        $user2->roles()->sync($role_user->id);

        $user->roles()->sync([$role_admin->id]);

        // Posts
        $post = Post::firstOrCreate(
            [
                'title' => 'Первый пост',
                'author_id' => $user->id
            ],
            [
                'posted_at' => now(),
                'content' => "
                    Добро пожаловать, в социальную сеть!"
            ]
        );
    }
}
