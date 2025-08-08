<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Models\AppSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BonusController extends AppController
{
    public function getInfo()
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();

        $bonus_info = $customer->getBonusInfo(self::VERSION);

        return api_response($bonus_info);
    }

    public function getHistory(Request $request)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        /** @var AppSetting $appSetting */
        $appSetting = AppSetting::first();
        if (!$appSetting->getBonusEnabled()) {
            return api_response(null);
        }
        try {
            $response = $this->http->withHeaders([
                'Accept' => 'application/json',
                'tenant-uuid' => $appSetting->loyalty_uuid,
            ])->get(config('loyalty.url') . "/" . self::VERSION . "/customers/$customer->id/bonus-history", $request->input());
        } catch (\Exception $e) {
            Log::warning('bonus service no available');
            return api_response(null);
        }

        return api_response($response->object(), $response->status());
    }

    public function getExpiration(Request $request)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        /** @var AppSetting $appSetting */
        $appSetting = AppSetting::first();
        if (!$appSetting->getBonusEnabled()) {
            return api_response(null);
        }
        try {
            $response = $this->http->withHeaders([
                'Accept' => 'application/json',
                'tenant-uuid' => $appSetting->loyalty_uuid,
            ])->get(config('loyalty.url') . "/" . self::VERSION . "/customers/$customer->id/bonus-expiration", $request->input());
        } catch (\Exception $e) {
            Log::warning('bonus service no available');
            return api_response(null);
        }

        return api_response($response->object());
    }
}
