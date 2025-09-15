<?php

namespace Modules\Membership\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MembershipPlanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'membership_type_id' => $this->membership_type_id,
            'title' => $this->title,
            'image' => $this->image,
            'price' => $this->price,
            'listing_limit' => $this->listing_limit,
            'gallery_images' => $this->gallery_images,
            'featured_listing' => $this->featured_listing,
            'enquiry_form' => $this->enquiry_form,
            'business_hour' => $this->business_hour,
            'membership_badge' => $this->membership_badge,
            'status' => $this->status,
            
            'membership_type' => $this->whenLoaded('membership_type', function () {
                return [
                    'id' => $this->membership_type->id,
                    'type' => $this->membership_type->type,
                    'validity' => $this->membership_type->validity,
                ];
            }),
            
            'features' => $this->whenLoaded('features', function () {
                return $this->features->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'feature' => $feature->feature,
                        'status' => $feature->status,
                    ];
                });
            }),
            
            'features_list' => $this->whenLoaded('features', function () {
                return $this->features->where('status', 1)->pluck('feature')->toArray();
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}