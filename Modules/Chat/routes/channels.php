<?php

use Illuminate\Support\Facades\Broadcast;
use Modules\Chat\app\Models\LiveChat;

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

// For user-specific live chat channels
Broadcast::channel('livechat-user-channel.{userId}.{memberId}', function ($user, $userId, $memberId) {
    return (int) $user->id === (int) $userId || (int) $user->id === (int) $memberId;
});

// For vendor/member-specific live chat channels  
Broadcast::channel('livechat-member-channel.{userId}.{memberId}', function ($user, $userId, $memberId) {
    return (int) $user->id === (int) $userId || (int) $user->id === (int) $memberId;
});

// Main chat channel for specific chat rooms
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = LiveChat::find($chatId);

    if (!$chat) {
        return false;
    }

    return $user->id === $chat->user_id || $user->id === $chat->member_id;
});

// Chat notifications channel
Broadcast::channel('chat-notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Chat status channel (typing, online status)
Broadcast::channel('chat-status.{chatId}', function ($user, $chatId) {
    $chat = LiveChat::find($chatId);

    if (!$chat) {
        return false;
    }

    return $user->id === $chat->user_id || $user->id === $chat->member_id;
});

// Vendor chats channel for new chat notifications
Broadcast::channel('vendor-chats.{vendorId}', function ($user, $vendorId) {
    // Check if user is the vendor and has appropriate role/permissions
    return (int) $user->id === (int) $vendorId && $user->hasRole('vendor');
});

// User chats channel for chat list updates
Broadcast::channel('user-chats.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});