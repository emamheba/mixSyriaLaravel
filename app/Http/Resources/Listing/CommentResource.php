<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => new UserResource($this->user),
            'replies' => ReplyResource::collection($this->replies),
            // 'created_at' => $this->created_at->diffForHumans()
            'created_at' => $this->created_at
        ];
    }
}
