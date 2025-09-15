<?php

namespace Modules\Membership\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_type_id',
        'title',
        'image',
        'price',
        'listing_limit',
        'gallery_images',
        'featured_listing',
        'enquiry_form',
        'business_hour',
        'membership_badge',
        'status'
    ];

    public function features()
    {
        return $this->hasMany(MembershipFeature::class,'membership_id','id');
    }

    public function membership_type()
    {
        return $this->belongsTo(MembershipType::class,'membership_type_id','id');
    }

    public function user_memberships()
    {
        return $this->hasMany(UserMembership::class,'membership_id','id');
    }

}
