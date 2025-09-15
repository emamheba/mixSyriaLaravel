<?php
namespace App\Repositories\Frontend\User;

use App\Models\Backend\Listing;
use App\Repositories\Main\EloquentMainRepository;

class ListingRepository extends EloquentMainRepository
{
  public function __construct(Listing $model)
    {
        parent::__construct($model);
    }

  

    public function getUserListings(int $userId, array $relations = [], int $perPage = 5)
    {
        return $this->builder(relations: $relations, condition: ['user_id' => $userId])
            ->paginate($perPage);
    }

    public function createListing(array $data): Listing
    {
        return $this->store($data);
    }


    public function updateListing(int $id, array $data): Listing
    {
        $listing = $this->findByCols(['id' => $id]);
        $listing->update($data);
        return $listing;
    }
}
