<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Controller;
use App\Services\Database\CatalogServiceV1;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AppController extends Controller
{
    protected CatalogServiceV1 $catalogService;
    protected PendingRequest $http;
    protected const VERSION = 'v1';

    public function __construct()
    {
        $this->catalogService = new CatalogServiceV1();
        $this->http = Http::withHeaders([
            'Accept' => 'application/json',
            'user' => auth('customer')->id(),
            'tenant-uuid' => app('currentTenant')->uuid,
        ]);
    }
}
