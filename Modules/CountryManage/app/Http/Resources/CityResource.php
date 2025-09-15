<?php

namespace Modules\CountryManage\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CountryManage\app\Http\Resources\CountryResource;
use Modules\CountryManage\app\Http\Resources\StateResource;

class CityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'city'       => $this->city,
            'country_id' => $this->country_id,
            'state_id'   => $this->state_id,
            'status'     => $this->status,
            'country'    => new CountryResource($this->whenLoaded('country')),
            'state'      => new StateResource($this->whenLoaded('state')),
        ];
    }
}
