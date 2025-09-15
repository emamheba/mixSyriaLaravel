<?php

namespace App\Repositories\Frontend\User;

use App\Models\Frontend\ListingAttribute;
use App\Repositories\Main\EloquentMainRepository;

class ListingAttributeRepository extends EloquentMainRepository
{
    public function __construct(ListingAttribute $model)
    {
        parent::__construct($model);
    }

    public function deleteByListingId(int $listingId): int
    {
        return $this->model::where('listing_id', $listingId)->delete();
    }

    public function createMany(int $listingId, array $attributes): bool
    {
        $data = array_map(function($attribute) use ($listingId) {
            return array_merge($attribute, ['listing_id' => $listingId]);
        }, $attributes);

        return $this->model::insert($data);
    }
}