<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
  public function toArray($request)
  {
    $otherParticipant = $this->user_id === auth()->id() ? $this->member : $this->user;

    return [
      'id' => $this->id,
      'other_participant' => [
        'id' => $otherParticipant->id,
        'full_name' => $otherParticipant->full_name, // Make sure you have this accessor in User model
        'image' => $otherParticipant->image_url, // Or whatever the image attribute is
      ],
      // ... aisha el baki 
    ];
  }


  private function getUnreadCount(): int
  {
    if (auth()->id() == $this->user_id) {
      return $this->user_unseen_msg_count ?? 0;
    }

    if (auth()->id() == $this->member_id) {
      return $this->member_unseen_msg_count ?? 0;
    }

    return 0;
  }
}