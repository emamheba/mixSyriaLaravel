<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'key' => $this->option_name,
            'value' => $this->option_value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}