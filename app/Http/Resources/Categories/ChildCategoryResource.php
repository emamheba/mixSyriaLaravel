<?php

namespace App\Http\Resources\Categories;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Categories\SubCategoryResource;
use Modules\Brand\app\Http\Resources\BrandResource;
use App\Http\Resources\MetaDataResource;

class ChildCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'brand_id' => $this->brand_id,
            // 'brand' => new BrandResource($this->brand),
            // 'category' => new CategoryResource($this->whenLoaded('category')),
            // 'subcategory'   => new SubCategoryResource($this->whenLoaded('subcategory')),
        ];
    }
}
