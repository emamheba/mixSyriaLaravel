<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Modules\Notification\App\Http\Controllers\Backend\NotificationAdminController;

// Admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::controller(NotificationAdminController::class)->group(function () {
        Route::match(['get', 'post'], 'notification/settings', 'settings')->name('admin.notification.settings')->permission('notification-settings');
        Route::get('notifications', 'index')->name('admin.notifications.index')->permission('view-notifications');
        Route::post('notifications/send', 'sendNotification')->name('admin.notifications.send')->permission('send-notifications');
    });
});

// User routes
Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth', 'userEmailVerify', 'globalVariable', 'setlang']], function () {
    Route::get('/notifications', [\Modules\Notification\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-all-read', [\Modules\Notification\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// Member routes  
Route::group(['prefix' => 'user/member', 'as' => 'member.', 'middleware' => ['auth', 'userEmailVerify', 'globalVariable', 'maintains_mode', 'setlang']], function () {
    Route::get('/notifications', [\Modules\Notification\App\Http\Controllers\MemberNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-all-read', [\Modules\Notification\App\Http\Controllers\MemberNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});