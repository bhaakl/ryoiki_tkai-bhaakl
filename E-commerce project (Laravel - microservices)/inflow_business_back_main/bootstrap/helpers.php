<?php

use App\Models\AppSetting;

if (! function_exists('api_response')) {
    /**
     * Return a new response from the application.
     *
     * @param  \Illuminate\Contracts\View\View|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function api_response($content = '', $status = 200, array $headers = [])
    {
        if ($content && \App\Models\Tenant::checkCurrent()) {
            /** @var AppSetting $app_settings */
            $app_settings = AppSetting::first();
            $content = json_encode($content);
            $content = strtr($content, $app_settings->getColors());
            $content = json_decode($content);
        }

        if (in_array($status, [200, 201]))
            return api_success($content, $status, $headers);

        return api_error($content->error ?? $content, $status, $headers);
    }
}

if (! function_exists('api_success')) {
    /**
     * Return a new response from the application.
     *
     * @param  \Illuminate\Contracts\View\View|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function api_success($content = '', $status = 200, array $headers = [])
    {
        return response()->api($content, $status, $headers);
    }
}

if (! function_exists('api_error')) {
    /**
     * Return a new response from the application.
     *
     * @param  \Illuminate\Contracts\View\View|string|array|null  $content
     * @param  int  $status
     * @param  array  $headers
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function api_error($content = '', $status = 400, array $headers = [])
    {
        return response()->api_error_response($content, $status, $headers);
    }
}
