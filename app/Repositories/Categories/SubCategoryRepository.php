<?php

namespace App\Repositories\Categories;

use App\Models\Backend\SubCategory;
use App\Repositories\Main\EloquentMainRepository;
use Illuminate\Database\Eloquent\Collection;

class SubCategoryRepository extends EloquentMainRepository
{
    public function __construct(SubCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get subcategories by category ID
     * 
     * @param int $categoryId
     * @return Collection
     */
    public function getSubCategoriesByBrand($brandId)
    {
        return SubCategory::where('brand_id', $brandId)->get();
    }
}