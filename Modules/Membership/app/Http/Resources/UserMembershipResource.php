<?php

namespace Modules\Membership\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMembershipResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'membership_id' => $this->membership_id,
            'membership' => $this->whenLoaded('membership'),
            'price' => $this->price,
            
            'initial_limits' => [
                'listing_limit' => $this->initial_listing_limit,
                'gallery_images' => $this->initial_gallery_images,
                'featured_listing' => $this->initial_featured_listing,
                'enquiry_form' => $this->initial_enquiry_form,
                'business_hour' => $this->initial_business_hour,
                'membership_badge' => $this->initial_membership_badge,
            ],
            
            'current_limits' => [
                'listing_limit' => $this->listing_limit,
                'gallery_images' => $this->gallery_images,
                'featured_listing' => $this->featured_listing,
                'enquiry_form' => $this->enquiry_form,
                'business_hour' => $this->business_hour,
                'membership_badge' => $this->membership_badge,
            ],
            
            'usage' => [
                'used_listings' => $this->initial_listing_limit - $this->listing_limit,
                'used_gallery_images' => $this->initial_gallery_images - $this->gallery_images,
                'used_featured_listings' => $this->initial_featured_listing - $this->featured_listing,
            ],
            
            'expire_date' => $this->expire_date,
            'payment_gateway' => $this->payment_gateway,
            'payment_status' => $this->payment_status,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status,
            
            'is_active' => $this->payment_status === 'complete' && now()->lt($this->expire_date),
            'is_expired' => $this->expire_date ? now()->gt($this->expire_date) : false,
            'days_remaining' => $this->expire_date ? now()->diffInDays($this->expire_date, false) : 0,
            'expires_soon' => $this->expire_date ? now()->diffInDays($this->expire_date, false) <= 7 : false,
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}