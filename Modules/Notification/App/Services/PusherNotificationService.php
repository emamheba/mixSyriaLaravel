<?php
// app/Services/PusherNotificationService.php

namespace Modules\Notification\App\Services;

use Modules\Notification\App\Models\Notification;
use Pusher\Pusher;

class PusherNotificationService
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            [
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'useTLS' => true
            ]
        );
    }

    public function broadcastNotification(Notification $notification)
    {
        $channel = 'private-notifications.' . $notification->user_id;
        $event = 'notification.created';
        
        $data = [
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
            'type' => $notification->type->slug,
            'data' => $notification->data,
            'created_at' => $notification->created_at->toISOString(),
            'is_read' => !is_null($notification->read_at)
        ];
        
        $this->pusher->trigger($channel, $event, $data);
    }

    public function broadcastNotificationRead(Notification $notification)
    {
        $channel = 'private-notifications.' . $notification->user_id;
        $event = 'notification.read';
        
        $data = [
            'id' => $notification->id,
            'read_at' => $notification->read_at->toISOString(),
        ];
        
        $this->pusher->trigger($channel, $event, $data);
    }

    public function broadcastNotificationDeleted($userId, $notificationId)
    {
        $channel = 'private-notifications.' . $userId;
        $event = 'notification.deleted';
        
        $data = [
            'id' => $notificationId,
        ];
        
        $this->pusher->trigger($channel, $event, $data);
    }
}