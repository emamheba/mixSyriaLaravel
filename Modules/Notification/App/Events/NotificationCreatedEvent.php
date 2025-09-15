<?php

namespace Modules\Notification\App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Notification\App\Models\Notification;

class NotificationCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }
    
    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notification->user_id);
    }
    
    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'type' => $this->notification->type->slug,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at->toISOString(),
            'is_read' => !is_null($this->notification->read_at)
        ];
    }
    
    public function broadcastAs()
    {
        return 'notification.created';
    }
}