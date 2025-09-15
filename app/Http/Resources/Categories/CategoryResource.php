<?php

namespace App\Http\Resources\Categories;

use App\Http\Resources\MetaDataResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Brand\app\Http\Resources\BrandResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'status' => $this->status,
            'slug' => $this->slug,
            'image' => $this->image,
            'subcategories' => SubCategoryResource::collection($this->subcategories()->latest()->get()),
            'brands' => BrandResource::collection($this->brands()->latest()->get()),
        ];
    }
}
