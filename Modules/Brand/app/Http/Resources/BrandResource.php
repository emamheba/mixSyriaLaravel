<?php

namespace Modules\Brand\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Categories\SubCategoryResource;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'url'            => $this->url,
            'image'          => $this->image,
            'category'       => new CategoryResource($this->whenLoaded('category')),
            'sub_categories' => SubCategoryResource::collection($this->whenLoaded('subCategories')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
