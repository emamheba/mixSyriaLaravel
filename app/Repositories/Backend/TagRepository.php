<?php
namespace App\Repositories\Backend;

use Modules\Blog\app\Models\Tag;
use App\Repositories\Main\EloquentMainRepository;

class TagRepository extends EloquentMainRepository
{
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }
    public function getActiveTags()
    {
        return $this->all(['*'], [], ['status' => 'publish']);
    }


    public function deleteByListingId(int $listingId): int
    {
        return $this->model::where('listing_id', $listingId)->delete();
    }

    public function insertMultiple(array $data): bool
    {
        return $this->model::insert($data);
    }
}
