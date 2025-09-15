<?php

namespace Modules\Chat\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\app\Models\LiveChat;

class LiveChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $livechat;
    public $messageBlade;
    public $senderType; // 'user' or 'vendor'
    public $senderId;
    public $receiverId;

    public function __construct(
        string $messageBlade,
        $message, 
        LiveChat $livechat,
        string $senderType,
        int $senderId,
        int $receiverId
    ) {
        $this->messageBlade = $messageBlade;
        $this->message = $message;
        $this->livechat = $livechat;
        $this->senderType = $senderType;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn(): array
    {
        if ($this->senderType === 'user') {
            return [
                new PrivateChannel('livechat-member-channel.' . $this->senderId . '.' . $this->receiverId),
            ];
        } else {
            return [
                new PrivateChannel('livechat-user-channel.' . $this->receiverId . '.' . $this->senderId),
            ];
        }
    }

    public function broadcastAs(): string
    {
        if ($this->senderType === 'user') {
            return 'livechat-member-' . $this->receiverId;
        } else {
            return 'livechat-user-' . $this->receiverId;
        }
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'livechat' => $this->livechat,
            'sender_type' => $this->senderType,
            'sender_id' => $this->senderId,
            'timestamp' => now()->toISOString()
        ];
    }
}