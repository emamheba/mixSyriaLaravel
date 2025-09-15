<?php

namespace Modules\Brand\app\Repositories;
use Modules\Brand\app\Models\Brand;
use App\Repositories\Main\EloquentMainRepository;

class BrandRepository extends EloquentMainRepository
{
    public function __construct(Brand $brand)
    {
        parent::__construct($brand);
    }

    /**
     * جلب الماركات بناءً على معرف التصنيف
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBrandsByCategory($categoryId)
    {
        return $this->all(['*'], [], ['category_id' => $categoryId]);
    }
}