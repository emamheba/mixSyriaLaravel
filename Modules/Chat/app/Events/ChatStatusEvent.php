<?php

namespace Modules\Chat\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $chatId;
    public $status; // 'online', 'offline', 'typing'
    public $userType; // 'user' or 'vendor'

    public function __construct($userId, $chatId, $status, $userType)
    {
        $this->userId = $userId;
        $this->chatId = $chatId;
        $this->status = $status;
        $this->userType = $userType;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat-status.' . $this->chatId);
    }

    public function broadcastAs()
    {
        return 'user-status';
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'status' => $this->status,
            'user_type' => $this->userType,
            'timestamp' => now()->toISOString()
        ];
    }
}