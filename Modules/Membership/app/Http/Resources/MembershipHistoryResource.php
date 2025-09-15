<?php

namespace Modules\Membership\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipHistoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'membership_id' => $this->membership_id,
            'membership_type' => $this->membership?->membership_type_id,
            'price' => $this->price,
            'listing_limit' => $this->listing_limit,
            'gallery_images' => $this->gallery_images,
            'featured_listing' => $this->featured_listing,
            'enquiry_form' => $this->enquiry_form,
            'business_hour' => $this->business_hour,
            'membership_badge' => $this->membership_badge,
            'expire_date' => $this->expire_date,
            'payment_gateway' => $this->payment_gateway,
            'payment_status' => $this->payment_status,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            'is_expired' => $this->expire_date ? now()->gt($this->expire_date) : false,
            'days_remaining' => $this->expire_date ? now()->diffInDays($this->expire_date, false) : 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
