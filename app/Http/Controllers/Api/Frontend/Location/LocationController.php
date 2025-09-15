<?php

namespace App\Http\Controllers\Api\Frontend\Location;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Location\Region;
use Illuminate\Http\Request;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\State;
use Modules\CountryManage\app\Models\District;

class LocationController extends Controller
{

    public function getCities()
    {
        $cities = City::with('state:id,state')
            ->get(['id', 'city', 'state_id']);

        return ApiResponse::success('Cities retrieved', $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->city,
                'state_id' => $city->state_id,
            ];
        }));
    }

    public function getStates()
    {
        $states = State::all(['id', 'state']);
        return ApiResponse::success('States retrieved', $states->map(function ($state) {
            return [
                'id' => $state->id,
                'name' => $state->state,
            ];
        }));
    }

    public function getCitiesByState(State $state)
    {
        $cities = $state->cities()->get(['id', 'city']);
        return ApiResponse::success('Cities retrieved', $cities->map(function($city){
            return [
                'id' => $city->id,
                'name' => $city->city,
            ];
        }));
    }

    public function getDistricts()
    {
        $districts = District::with(['city:id,city', 'state:id,state'])
            ->get(['id', 'district', 'city_id', 'state_id']);

        return ApiResponse::success('Districts retrieved', $districts->map(function ($district) {
            return [
                'id' => $district->id,
                'name' => $district->district,
                'city_id' => $district->city_id,
                'state_id' => $district->state_id,
            ];
        }));
    }

    public function getDistrictsByCity(City $city)
    {
        $districts = $city->districts()->get(['id', 'district']);
        return ApiResponse::success('Districts retrieved', $districts->map(function($district){
            return [
                'id' => $district->id,
                'name' => $district->district,
            ];
        }));
    }
}