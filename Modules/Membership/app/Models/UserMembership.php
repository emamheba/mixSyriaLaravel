<?php

namespace Modules\Membership\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Membership\Database\factories\UserMembershipFactory;

class UserMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_id',
        'price',
        'initial_listing_limit',
        'initial_gallery_images',
        'initial_featured_listing',
        'initial_enquiry_form',
        'initial_business_hour',
        'initial_membership_badge',
        'listing_limit',
        'gallery_images',
        'featured_listing',
        'enquiry_form',
        'business_hour',
        'membership_badge',
        'expire_date',
        'payment_gateway',
        'payment_status',
        'transaction_id',
        'manual_payment_image',
        'status'
    ];

    protected $casts = ['status'=>'integer'];

    public function membership()
    {
        return $this->belongsTo(Membership::class,'membership_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    protected static function newFactory(): UserMembershipFactory
    {
    }
}
