<?php

namespace Modules\Notification\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'default_channels',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_channels' => 'array',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function userSettings(): HasMany
    {
        return $this->hasMany(UserNotificationSetting::class);
    }
}