<?php

namespace App\Http\Resources\Categories;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Categories\ChildCategoryResource;
use Modules\Brand\app\Http\Resources\BrandResource;
use App\Http\Resources\MetaDataResource;

class SubCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'image' => $this->image,
            'status' => $this->stauts,
            'slug' => \Str::slug($this->name),
            'category_id' => $this->category_id,
            'brand_id' => $this?->brand_id,
            // 'childcategories' => SubCategoryResource::collection($this?->subCategories),
            'childcategories' => ChildCategoryResource::collection($this?->childcategories),

            // 'brand' => new BrandResource($this?->brand),
        ];
    }
}
