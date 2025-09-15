<?php

namespace Modules\Membership\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Membership\Database\factories\MembershipFeatureFactory;

class MembershipFeature extends Model
{
    use HasFactory;

    protected $fillable = ['membership_id','feature','status'];

    protected static function newFactory(): MembershipFeatureFactory
    {
    }
}
