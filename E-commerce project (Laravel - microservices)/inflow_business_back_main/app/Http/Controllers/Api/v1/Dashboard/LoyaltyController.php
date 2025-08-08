<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\LoyaltyTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\LoyaltySettingUpdateRequest;
use App\Http\Resources\v1\Dashboard\LoyaltySettingResource;
use App\Models\AppSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    protected Tenant $tenant;
    protected AppSetting $appSetting;
    protected LoyaltyService $loyaltyService;

    protected const VERSION = 'v1';

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
        $this->appSetting = AppSetting::first();
        $this->loyaltyService = new LoyaltyService(self::VERSION);
    }

    public function index()
    {
        $loyalty = new \stdClass();
        $loyalty->type = $this->appSetting->loyalty_type->value;

        if ($this->appSetting->loyalty_type == LoyaltyTypes::BONUS && $this->appSetting->loyalty_uuid) {
            $bonus_settings = $this->loyaltyService->getBonusSetting();
            $bonus_levels = $this->loyaltyService->getBonusLevels();

            $loyalty->bonus = new \stdClass();
            $loyalty->bonus->settings = $bonus_settings?->data;
            $loyalty->bonus->levels = $bonus_levels?->data;
        }

        return api_response(new LoyaltySettingResource(collect($loyalty)));
    }

    public function updateLoyaltySetting(LoyaltySettingUpdateRequest $request)
    {
        $this->appSetting->update(['loyalty_type' => $request->loyalty_type]);

        if ($this->appSetting->loyalty_type == LoyaltyTypes::BONUS) {
            $response = $this->loyaltyService->updateBonusSettings($request);
            if ($response->status() != 200) {
                sleep(2);
                $response = $this->loyaltyService->updateBonusSettings($request);
                if ($response->status() != 200) {
                    return api_response($response->json(), $response->status());
                }
            }
        }

        return $this->index();
    }

    public function createBonusLevel(Request $request)
    {
        if ($this->appSetting->loyalty_type == LoyaltyTypes::BONUS) {
            $response = $this->loyaltyService->createBonusLevel($request);
            if ($response->status() != 200) {
                return api_response($response->json(), $response->status());
            }
        }

        return $this->index();
    }

    public function updateBonusLevel($id, Request $request)
    {
        if ($this->appSetting->loyalty_type == LoyaltyTypes::BONUS) {
            $response = $this->loyaltyService->updateBonusLevel($id, $request);
            if ($response->status() != 200) {
                return api_response($response->json(), $response->status());
            }
        }

        return $this->index();
    }

    public function deleteBonusLevel($id)
    {
        if ($this->appSetting->loyalty_type == LoyaltyTypes::BONUS) {
            $response = $this->loyaltyService->deleteBonusLevel($id);
            if ($response->status() != 200) {
                return api_response($response->json(), $response->status());
            }
        }

        return $this->index();
    }
}
