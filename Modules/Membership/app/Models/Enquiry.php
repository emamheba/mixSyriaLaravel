<?php

namespace Modules\Membership\app\Models;

use App\Models\Backend\Listing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Membership\Database\factories\EnquiryFactory;

class Enquiry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id', 'listing_id', 'name','email','phone','message'];

    protected static function newFactory(): EnquiryFactory
    {
    }


    public function listing()
    {
        return $this->belongsTo(Listing::class,'listing_id','id');
    }

}
