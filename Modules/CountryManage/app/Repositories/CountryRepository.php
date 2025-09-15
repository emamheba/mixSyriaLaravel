<?php

namespace Modules\CountryManage\app\Repositories;
use App\Repositories\Main\EloquentMainRepository;
use Modules\CountryManage\app\Models\Country;

class CountryRepository extends EloquentMainRepository
{
  public function __construct(Country $model)
  {
      parent::__construct($model);
  }
    
    public function getAllCountries()
    {
        return Country::all();
    }

  
}