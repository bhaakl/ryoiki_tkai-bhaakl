<?php

namespace App\Http\Resources\v1\Dashboard\Acquiring;

use App\Models\Acquiring;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcquiringDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Acquiring $model */
        $model = $this->resource;

        $keys = [];
        foreach ($model->keys as $key => $val) {
            try {
                $keys[$key] = decrypt($val);
            } catch (\Exception $exception) {
                $keys[$key] = $val;
            }
        }

        return [
            'id' => $model->id,
            'name' => $model->name,
            'keys' => $keys,
            'ffd' => $model->ffd,
            'ffd_keys' => $model->ffd_keys
        ];
    }
}
