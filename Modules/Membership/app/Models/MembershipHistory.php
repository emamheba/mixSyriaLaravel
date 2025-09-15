<?php

namespace Modules\Membership\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipHistory extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'membership_id',
        'price',
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

    public function membership(){
        return $this->belongsTo(Membership::class,'membership_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

}
