<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\TagResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        $galleryImages = [];

        if (!is_null($this->gallery_images)) {
            $ids = explode('|', $this->gallery_images);

            foreach ($ids as $id) {
                if (!empty($id)) {
                    $url  = get_image_url_id_wise($id);
                    $galleryImages[] = [
                        'id' => $id,
                        'url' => $url,
                    ];
                }
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'description' => strip_tags($this->description),
            'listing_type' => $this->listing_type,
            'condition' => $this->condition,
            'phone_hidden' => $this->phone_hidden,
            'negotiable' => $this->negotiable,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'image' => $this->image?? [],
            'images' => $galleryImages,
            'views_count' => $this->view,
            'comment_count' => $this->comments_count,
            'user' => new UserResource($this->user),
            'featured' => $this->is_featured,
            'category_name' => $this?->category?->name,
            'sub_category_name' => $this?->subCategory?->name,
            'child_category_name' => $this?->childCategory?->name,
            'brand_name' => $this?->brand?->name,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'child_category_id' => $this->child_category_id,
            'brand_id' => $this->brand_id,
            'address' => $this?->address,
            'city' => $this?->city?->city,
            'state' => $this?->state?->state,
            'city_id' => $this->city_id,
            'state_id' => $this->state_id,
            'district_id' => $this->district_id,
            'tags' => TagResource::collection($this->tags),
            'comments' => CommentResource::collection($this->comments),
            'created_at' => $this->published_at,
            'status' => $this->status,
            // 'related' => ListingResource::collection($this->relatedListings),
        ];
    }
}
