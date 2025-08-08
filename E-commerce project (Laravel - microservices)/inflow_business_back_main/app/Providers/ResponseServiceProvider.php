<?php

namespace App\Providers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    public function boot(ResponseFactory $factory): void
    {
        $factory->macro('api', function ($data) use ($factory) {

            $customFormat = [
                'success' => 1,
                'data' => $data,
                'error' => null
            ];
            return $factory->make($customFormat, 200);
        });

        $factory->macro('api_error_response', function ($error, $status = 400) use ($factory) {

            $customFormat = [
                'success' => 0,
                'data' => null,
                'error' => $error
            ];
            return $factory->make($customFormat, $status);
        });
    }

    public function register(): void
    {
    }
}
