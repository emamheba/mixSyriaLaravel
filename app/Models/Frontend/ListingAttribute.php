<?php

namespace App\Models\Frontend;

use App\Models\Backend\Listing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'title',
        'description',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

}
