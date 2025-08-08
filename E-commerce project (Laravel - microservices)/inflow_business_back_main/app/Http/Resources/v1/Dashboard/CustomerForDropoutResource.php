<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Enums\LoyaltyTypes;
use App\Models\AppSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerForDropoutResource extends JsonResource
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

        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}
