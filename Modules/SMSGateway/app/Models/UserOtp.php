<?php

namespace Modules\SMSGateway\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\SMSGateway\Database\factories\UserOtpFactory;

class UserOtp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'user_otps';
    protected $fillable = ['user_id', 'otp_code', 'expire_date'];
    protected $dates = ['expire_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
