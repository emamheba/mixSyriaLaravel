<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\TagResource;
use App\Services\Listing\ListingRefreshService;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray($request)
    {
        $refreshService = new ListingRefreshService();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'description' => strip_tags($this->description),
            'listing_type' => $this->listing_type,
            'condition' => $this->condition,
            'image' => $this->image,
            'views_count' => $this->view,
            'comments_count' => $this->comments_count,
            'distance_km' => $this->when(isset($this->distance_km), $this->distance_km),
            'status' => $this->status,
            // 'distance' => $this->distanceFrom($request->lat, $request->lon),
            'featured' => $this->is_featured,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'child_category_id' => $this->child_category_id,
            'brand_id' => $this->brand_id,
            'address' => $this?->address,
            'city' => $this?->city?->city,
            'state' => $this?->state?->state,
            'district' => $this?->district?->district,
            'tags' => TagResource::collection($this->tags),
            'created_at' => $this->created_at,
            // 'can_refresh' => $this->when(
            //     $request->user()?->id === $this->user_id,
            //     $refreshService->canRefresh($this->resource)
            // ),
            // 'next_refresh_date' => $this->when(
            //     $request->user()?->id === $this->user_id,
            //     $refreshService->getNextRefreshDate($this->resource)
            // ),
            // 'days_until_refresh' => $this->when(
            //     $request->user()?->id === $this->user_id,
            //     $refreshService->getDaysUntilNextRefresh($this->resource)
            // ),
        ];
    }
}
