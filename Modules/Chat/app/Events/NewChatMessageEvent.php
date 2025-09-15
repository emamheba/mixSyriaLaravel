<?php

namespace Modules\Chat\app\Events;

use App\Http\Resources\Chat\ChatMessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\app\Models\LiveChatMessage;

class NewChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(LiveChatMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // Broadcast to both user and member channels for real-time updates
        $channels = [];
        
        // Channel for the recipient (user if message is from member, member if message is from user)
        if ($this->message->from_user == 1) {
            // Message from user, notify member
            $channels[] = new PrivateChannel('livechat-member-channel.' . $this->message->liveChat->user_id . '.' . $this->message->liveChat->member_id);
        } else {
            // Message from member, notify user  
            $channels[] = new PrivateChannel('livechat-user-channel.' . $this->message->liveChat->member_id . '.' . $this->message->liveChat->user_id);
        }
        
        // Also broadcast to the chat room channel for consistency
        $channels[] = new PrivateChannel('chat.' . $this->message->live_chat_id);
        
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return array_merge(
            (new ChatMessageResource($this->message))->resolve(),
            [
                'event_type' => 'new_message',
                'timestamp' => now()->toISOString(),
                'sender_type' => $this->message->from_user == 1 ? 'user' : 'vendor'
            ]
        );
    }
}