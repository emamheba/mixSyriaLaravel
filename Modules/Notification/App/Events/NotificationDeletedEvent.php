<?php

namespace Modules\Notification\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationDeletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $notificationId;
    
    public function __construct($userId, $notificationId)
    {
        $this->userId = $userId;
        $this->notificationId = $notificationId;
    }
    
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }
    
    public function broadcastWith()
    {
        return [
            'id' => $this->notificationId,
        ];
    }
    
    public function broadcastAs()
    {
        return 'notification.deleted';
    }
}