<?php

namespace App\Http\Resources\v1\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'email_verified_at' => $this->resource->email_verified_at,
            'phone_verified_at' => $this->resource->phone_verified_at,
        ];
    }
}
