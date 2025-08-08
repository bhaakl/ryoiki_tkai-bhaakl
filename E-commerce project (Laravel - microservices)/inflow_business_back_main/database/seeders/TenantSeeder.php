<?php

namespace Database\Seeders;

use App\Enums\AuthTypes;
use App\Enums\Roles;
use App\Enums\SocialNetworks;
use App\Models\AppSetting;
use App\Models\City;
use App\Models\Role;
use App\Models\SocialLink;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!app()->isProduction()) {
            $subdomains = [AuthTypes::Email->value];

            foreach ($subdomains as $subdomain) {
                $user = User::factory([
                    'email' => 'chief@test.ru',
                    'phone' => '79998880011',
                    'password' => bcrypt('secret')
                ])->create();
                $role = Role::where('name', Roles::CHIEF->value)->first();
                $user->assignRole($role);

                $domain = config("app.url");
                $domain = explode("://", $domain);
                $domain = $subdomain . '.' . $domain[1];

                sleep(5);
                /** @var Tenant $tenant */
                $tenant = Tenant::create([
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'name' => $subdomain,
                    'domain' => $domain,
                    'database' => "tenant_{$user->id}",
                    'uuid' => '0eabf35d-a2a3-4c9a-b1a8-9691373902f3'
                ]);
                $tenant->makeCurrent();
                $user->update(['tenant_id' => $tenant->id]);

                City::factory(1)->create();

                $app_settings = AppSetting::factory()->create([
                    'auth_type' => $subdomain,
                ]);

                try {
                    $app_settings->addMediaFromUrl('https://loremflickr.com/300/300')->toMediaCollection('logo_pdf');
                    $app_settings->addMediaFromUrl('https://loremflickr.com/1024/1024')->toMediaCollection('logo_png');
                } catch (\Exception $exception) {}

                $network = SocialNetworks::random();
                SocialLink::factory(['network' => $network->name])->create();
                $this->call(AboutSeeder::class);
                $this->call(MainPageSeeder::class);
            }
        }
    }
}
