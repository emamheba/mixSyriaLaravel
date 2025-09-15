<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'chat_id' => $this->live_chat_id,
            'message' => $this->message,
            'file_url' => $this->file ? asset($this->file) : null, 
            'is_seen' => (bool) $this->is_seen,
            'sent_by_me' => $this->isSentByAuthUser(),
            'sender_type' => $this->from_user == 1 ? 'user' : 'member', 
            'created_at' => $this->created_at->diffForHumans(), 
            'timestamp' => $this->created_at,
        ];
    }

    private function isSentByAuthUser(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $chat = $this->liveChat; 
        $authUserIsUser = (auth()->id() === $chat->user_id);
        $authUserIsMember = (auth()->id() === $chat->member_id);

        if ($authUserIsUser && $this->from_user == 1) {
            return true;
        }

        if ($authUserIsMember && $this->from_user == 2) {
            return true;
        }
        
        return false;
    }
}