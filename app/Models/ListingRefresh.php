<?php

namespace App\Models;

use App\Models\Backend\Listing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingRefresh extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'listing_id',
        'user_id',
        'refreshed_at',
        'ip_address',
        'user_agent',
    ];
    
    protected $casts = [
        'refreshed_at' => 'datetime',
    ];
    
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}