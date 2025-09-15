<?php

namespace Modules\CountryManage\app\Repositories;

use Modules\CountryManage\app\Models\State;
use App\Repositories\Main\EloquentMainRepository;

class StateRepository extends EloquentMainRepository
{
    public function __construct(State $state)
    {
        parent::__construct($state);
    }

    /**
     * جلب الولايات بناءً على معرف الدولة
     *
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStatesByCountry($countryId)
    {
        return $this->all(condition: ['country_id' => $countryId]);
    }
}
