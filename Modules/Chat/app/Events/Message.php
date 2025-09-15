<?php

namespace Modules\Chat\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $userId;

    public function __construct($notification, $userId)
    {
        $this->notification = $notification;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat-notifications.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'chat-notification';
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification,
            'timestamp' => now()->toISOString()
        ];
    }
}