<?php

namespace Modules\Wallet\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Wallet\Database\factories\WalletFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','balance','status'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    protected static function newFactory(): WalletFactory
    {

    }
}
