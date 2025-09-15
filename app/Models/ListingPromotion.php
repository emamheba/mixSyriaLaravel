<?php

namespace App\Models;

use App\Models\Backend\Listing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingPromotion extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'listing_id',
        'promotion_package_id',
        'payment_method',
        'payment_status',
        'transaction_id',
        'bank_transfer_proof_path',
        'payment_confirmed_at',
        'starts_at',
        'expires_at',
        'amount_paid',
        'admin_notes',
    ];

    protected $casts = [
        'payment_confirmed_at' => 'datetime',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function promotionPackage()
    {
        return $this->belongsTo(PromotionPackage::class);
    }
}
