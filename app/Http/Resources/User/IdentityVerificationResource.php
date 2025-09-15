<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class IdentityVerificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'identification_type' => $this->identification_type,
            'identification_number' => $this->identification_number,
            'front_document' => $this->front_document,
            'back_document' => $this->back_document,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'zip_code' => $this->zip_code,
            'address' => $this->address,
            'verify_by' => $this->verify_by,
            'status' => $this->status,
            'status_text' => $this->getStatusText(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'country' => $this->whenLoaded('user_country', function () {
                return [
                    'id' => $this->user_country->id,
                    'name' => $this->user_country->country,
                    'status' => $this->user_country->status,
                ];
            }),
            'state' => $this->whenLoaded('user_state', function () {
                return [
                    'id' => $this->user_state->id,
                    'name' => $this->user_state->state,
                ];
            }),
            'city' => $this->whenLoaded('user_city', function () {
                return [
                    'id' => $this->user_city->id,
                    'name' => $this->user_city->city,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            }),
        ];
    }

    private function getStatusText(): string
    {
        return match($this->status) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Unknown'
        };
    }
}