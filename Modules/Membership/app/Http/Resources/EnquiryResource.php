<?php

namespace Modules\Membership\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnquiryResource extends JsonResource
{
  
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'listing_id' => $this->listing_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'listing' => $this->whenLoaded('listing', function () {
                return [
                    'id' => $this->listing->id,
                    'title' => $this->listing->title,
                    'slug' => $this->listing->slug,
                ];
            }),
        ];
    }
}