<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['name', 'city_id', 'status'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
