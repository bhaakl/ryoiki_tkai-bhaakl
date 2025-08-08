<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\LoyaltyTypes;
use App\Models\AppSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Customer $model */
        $model = $this->resource;

        /** @var AppSetting $settings */
        $settings = AppSetting::first();

        return [
            'id' => $model->id,
            'name' => $model->name,
            'email' => $model->email,
            'phone' => $model->phone,
            'birthday' => $model->birthday,
            'push_notifications' => $model->push_notifications,
            $this->mergeWhen($settings->loyalty_type == LoyaltyTypes::BONUS, [
                'bonus_level' => $model->getBonusInfo()?->levels?->current?->name,
                'bonus_balance' => $model->getBonusBalance(),
            ])
        ];
    }
}
