<?php
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
        return response()->api_error($content, $status, $headers);
    }
}
