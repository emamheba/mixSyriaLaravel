<?php

namespace Modules\Chat\app\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\app\Models\LiveChat;

class ChatCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $vendorId;

    public function __construct(LiveChat $chat, $vendorId)
    {
        $this->chat = $chat;
        $this->vendorId = $vendorId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('vendor-chats.' . $this->vendorId);
    }

    public function broadcastAs()
    {
        return 'new-chat';
    }

    public function broadcastWith()
    {
        return [
            'chat' => $this->chat->load('user'),
            'timestamp' => now()->toISOString()
        ];
    }
}