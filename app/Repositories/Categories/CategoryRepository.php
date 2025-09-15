<?php

namespace App\Repositories\Categories;

use App\Models\Backend\Category;
use App\Repositories\Main\EloquentMainRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends EloquentMainRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active categories
     * 
     * @return Collection
     */
    public function getActiveCategories(): Collection
    {
        return $this->all(['*'], [], ['status' => 1]);
    }
}