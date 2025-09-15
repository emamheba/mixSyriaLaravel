<?php

namespace Modules\CountryManage\app\Repositories;

use Modules\CountryManage\app\Models\City;
use App\Repositories\Main\EloquentMainRepository;

class CityRepository extends EloquentMainRepository
{
    public function __construct(City $city)
    {
        parent::__construct($city);
    }

    /**
     * جلب المدن بناءً على معرف الولاية
     *
     * @param int $stateId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCitiesByState($stateId)
    {
      return $this->all(condition: ['state_id' => $stateId]);
    }

  
}
