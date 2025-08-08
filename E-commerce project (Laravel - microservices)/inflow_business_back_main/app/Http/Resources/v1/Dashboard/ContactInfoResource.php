<?php

namespace App\Http\Resources\v1\Dashboard;

use App\Http\Resources\v1\App\SocialLinkResource;
use App\Models\SocialLink;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Tenant $model */
        $model = $this->resource;

        return [
            'email'        => $model->email,
            'phone'        => $model->phone,
            'social_links' => SocialLinkResource::collection(SocialLink::all()),
        ];
    }
}
