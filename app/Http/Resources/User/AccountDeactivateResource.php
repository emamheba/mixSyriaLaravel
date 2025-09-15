<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountDeactivateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'reason' => $this->reason,
            'description' => $this->description,
            'status' => $this->status,
            'account_status' => $this->account_status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'user' => $this->whenLoaded('user', function () {
                return UserResource::make($this->user);
            }),
        ];
    }
}
