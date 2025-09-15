<?php

namespace Modules\Membership\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipPurchaseResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'membership_id' => $this->membership_id,
      'purchase_details' => [
        'price' => $this->price,
        'payment_gateway' => $this->payment_gateway,
        'payment_status' => $this->payment_status,
        'transaction_id' => $this->transaction_id,
        'manual_payment_image' => $this->manual_payment_image ?
          asset('assets/uploads/manual-payment/membership/' . $this->manual_payment_image) : null,
      ],

      'user' => [
        'id' => $this->user_id,
        'name' => $this->user->first_name . ' ' . $this->user->last_name,
        'email' => $this->user->email,
      ],
      'membership' => [
        'id' => $this->membership->id,
        'title' => $this->membership->title,
        'type' => $this->membership->membership_type->type ?? null,
        'validity_days' => $this->membership->membership_type->validity ?? null,
      ],


      // 'membership_features' => [
      //   'listing_limit' => $this->initial_listing_limit,
      //   'gallery_images' => $this->initial_gallery_images,
      //   'featured_listing' => $this->initial_featured_listing,
      //   'enquiry_form' => $this->initial_enquiry_form,
      //   'business_hour' => $this->initial_business_hour,
      //   'membership_badge' => $this->initial_membership_badge,
      // ],

      'dates' => [
        'purchase_date' => $this->created_at,
        'expire_date' => $this->expire_date,
        'is_active' => $this->payment_status === 'complete' && now()->lt($this->expire_date),
        'is_expired' => $this->expire_date ? now()->gt($this->expire_date) : false,
        'days_remaining' => $this->expire_date ? now()->diffInDays($this->expire_date, false) : 0,
        'expires_soon' => $this->expire_date ? now()->diffInDays($this->expire_date, false) <= 7 : false,
      ],
      'status' => $this->status,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}