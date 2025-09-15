<?php

namespace App\Repositories\Categories;

use App\Models\Backend\ChildCategory;
use App\Repositories\Main\EloquentMainRepository;
use Illuminate\Database\Eloquent\Collection;

class ChildCategoryRepository extends EloquentMainRepository
{
    public function __construct(ChildCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get child categories by subcategory ID
     * 
     * @param int $subCategoryId
     * @return Collection
     */
    public function getChildCategoriesBySubCategory(int $subCategoryId): Collection
    {
        return $this->all(['*'], [], ['sub_category_id' => $subCategoryId, 'status' => 1]);
    }
}