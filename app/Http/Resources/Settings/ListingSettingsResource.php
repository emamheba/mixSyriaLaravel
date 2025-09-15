<?php

namespace App\Http\Resources\Settings;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingSettingsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'listing_create_settings' => $this->resource['listing_create_settings'] ?? null,
            'listing_create_status_settings' => $this->resource['listing_create_status_settings'] ?? null,
            'updated_at' => now(),
        ];
    }
}