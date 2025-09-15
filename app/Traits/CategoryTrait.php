<?php
namespace App\Traits;

use App\Models\Backend\SubCategory;
use Modules\Brand\app\Models\Brand;


trait CategoryTrait
{
    public function getBrandsByCategory($category_id)
    {
        return Brand::where('category_id', $category_id)->get();
    }

    public function getSubCategoriesByBrand($brand_id)
    {
        return SubCategory::where('brand_id', $brand_id)->get();
    }
}