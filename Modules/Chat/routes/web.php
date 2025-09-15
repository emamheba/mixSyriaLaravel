<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\app\Http\Controllers\ChatController;
use Modules\Chat\app\Http\Controllers\Backend\PusherSettingsController;
use Modules\Chat\app\Http\Controllers\MemberChatController;


//users routes
Route::group(['prefix'=>'user/live','as'=>'user.','middleware'=>['auth','userEmailVerify','globalVariable','setlang']],function() {
    Route::get('/chat', [ChatController::class, 'live_chat'])->name('live.chat');
    Route::post("/fetch-chat-member-record", [ChatController::class,'fetch_chat_record'])->name("fetch.chat.member.record");
    Route::post('/message-send', [ChatController::class,'message_send'])->name("message.send");
});

Route::post('/user/get-message-template', [ChatController::class, 'getMessageTemplate'])->name('user.get.message.template');

//member routes
Route::group(['prefix'=>'user/member/live','as'=>'member.','middleware'=>['auth','userEmailVerify','globalVariable', 'maintains_mode','setlang']],function() {
    Route::get('/chat', [MemberChatController::class, 'live_chat'])->name('live.chat');
    Route::post("fetch-chat-user-record", [MemberChatController::class,'fetch_chat_record'])->name("fetch.chat.user.record");
    Route::post('/message-send', [MemberChatController::class,'message_send'])->name("message.send");
});

Route::post('/member/get-message-template', [MemberChatController::class, 'getMessageTemplate'])->name('member.get.message.template');

//admin routes
Route::group(['prefix'=>'admin','middleware' => ['auth:admin','setlang']],function(){
    Route::controller(PusherSettingsController::class)->group(function () {
        Route::match(['get','post'],'pusher/settings', [PusherSettingsController::class, 'pusher_settings'])->name('admin.pusher.settings')->permission('live-chat-settings');
    });
});
