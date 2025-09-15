<?php

namespace Modules\Chat\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_id',
        'admin_id',
    ];

    protected $withCount = [
        'member_unseen_msg',
        'user_unseen_msg'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, "member_id", "id");
    }

    public function livechatMessage(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, "live_chat_id", "id");
    }

    public function member_unseen_msg(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, "live_chat_id", "id")
            ->where("live_chat_messages.from_user", 1)
            ->where("live_chat_messages.is_seen", 0);
    }

    public function user_unseen_msg(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, "live_chat_id", "id")
            ->where("live_chat_messages.from_user", 2)
            ->where("live_chat_messages.is_seen", 0);
    }

    public function latestMessage()
    {
        return $this->hasOne(LiveChatMessage::class, 'live_chat_id')->latestOfMany();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class, "live_chat_id", "id");
    }

    // Helper method to get the other participant in the chat
    public function getOtherParticipant($currentUserId)
    {
        if ($this->user_id == $currentUserId) {
            return $this->member;
        }
        return $this->user;
    }

    // Scope for user chats
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope for member chats
    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    // Scope for participant
    public function scopeForParticipant($query, $userId, $memberId)
    {
        return $query->where(function ($q) use ($userId, $memberId) {
            $q->where('user_id', $userId)
              ->where('member_id', $memberId);
        })->orWhere(function ($q) use ($userId, $memberId) {
            $q->where('user_id', $memberId)
              ->where('member_id', $userId);
        });
    }
}