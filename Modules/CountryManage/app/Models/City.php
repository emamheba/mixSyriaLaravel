<?php

namespace Modules\CountryManage\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class City extends Model
{
  use HasFactory;


  protected $fillable = ['city', 'country_id', 'state_id', 'status'];
  protected $casts = ['status' => 'integer'];

  protected static function newFactory()
  {
    return \Modules\CountryManage\Database\factories\CityFactoryFactory::new();
  }

  public static function all_cities()
  {
    return self::select(['id', 'city', 'country_id', 'state_id', 'status'])->where('status', 1)->get();
  }

  public function country()
  {
    return $this->belongsTo(Country::class);
  }

  public function state()
  {
    return $this->belongsTo(State::class);
  }

  // In City.php

  public function districts()
  {
    return $this->hasMany(District::class);
  }
}
