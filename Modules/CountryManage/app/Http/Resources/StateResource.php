<?php

namespace Modules\CountryManage\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CountryManage\app\Http\Resources\CountryResource;

class StateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->state,
            'country_id' => $this->country_id,
            'status'     => $this->status,
            'timezone'   => $this->timezone,
            'country'    => new CountryResource($this->whenLoaded('country')),
        ];
    }
}
