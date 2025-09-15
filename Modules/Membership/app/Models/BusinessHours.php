<?php

namespace Modules\Membership\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Membership\Database\factories\BusinessHoursFactory;

class BusinessHours extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'day_of_week'];

    protected static function newFactory(): BusinessHoursFactory
    {
    }
}
