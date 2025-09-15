<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'country_id', 'status'];

    public function conutry()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}