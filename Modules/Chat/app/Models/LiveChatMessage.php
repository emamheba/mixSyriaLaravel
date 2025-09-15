<?php

namespace Modules\Chat\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        "live_chat_id",
        "from_user",
        "message",
        "file",
        "is_seen",
    ];

    protected $casts = [
        "message" => "json",
        "created_at" => "datetime",
        "updated_at" => "datetime",
        "is_seen" => "boolean" // Changed to boolean for clarity
    ];

    protected $appends = [
        'file_url'
    ];

    public function liveChat(): BelongsTo
    {
        return $this->belongsTo(LiveChat::class, "live_chat_id", "id");
    }

    public function sender()
    {
        return $this->belongsTo(User::class, function ($query) {
            $fromUser = $this->from_user;
            $chat = $this->liveChat;
            
            if ($fromUser == 1) {
                return $chat->user_id;
            } else {
                return $chat->member_id;
            }
        }, 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, function ($query) {
            $fromUser = $this->from_user;
            $chat = $this->liveChat;
            
            if ($fromUser == 1) {
                return $chat->member_id;
            } else {
                return $chat->user_id;
            }
        }, 'id');
    }

    // Get file URL attribute
    public function getFileUrlAttribute()
    {
        if (!$this->file) {
            return null;
        }
        
        return asset('storage/' . $this->file);
    }

    // Get file path attribute
    public function getFilePathAttribute()
    {
        return $this->file;
    }

    // Scope for unseen messages
    public function scopeUnseen($query)
    {
        return $query->where('is_seen', false);
    }

    // Scope for messages from specific user type
    public function scopeFromUserType($query, $userType)
    {
        return $query->where('from_user', $userType);
    }

    // Mark message as seen
    public function markAsSeen()
    {
        $this->is_seen = true;
        $this->save();
        
        return $this;
    }
}