<?php
// routes/channels.php

use Illuminate\Support\Facades\Broadcast;
use Modules\Notification\App\Models\Notification;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// User-specific notification channels
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Notification settings channel
Broadcast::channel('notification-settings.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Admin notification channel
Broadcast::channel('admin-notifications.{adminId}', function ($admin, $adminId) {
    return (int) $admin->id === (int) $adminId && $admin->hasRole('admin');
});