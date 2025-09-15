<?php

namespace Modules\CountryManage\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\CountryManage\app\Http\Resources\StateResource;

class CountryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            // تحويل اسم البلد من عمود "country" إلى "name"
            'name'         => $this->country,
            'country_code' => $this->country_code,
            'dial_code'    => $this->dial_code,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'status'       => $this->status,
            'states'       => StateResource::collection($this->whenLoaded('states')),
        ];
    }
}
