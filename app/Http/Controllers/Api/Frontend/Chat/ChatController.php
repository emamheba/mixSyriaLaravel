<?php

namespace App\Http\Controllers\Api\Frontend\Chat;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Chat\ChatMessageResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Models\LiveChatMessage;
use Modules\Chat\app\Services\UserChatServiceApi;

class ChatController extends Controller
{
    public function __construct(private UserChatServiceApi $chatService)
    {
    }


    public function index(): JsonResponse
    {
        $userId = Auth::id();

        $chats = LiveChat::query()
            ->where(fn($query) => $query->where('user_id', $userId)->orWhere('member_id', $userId))
            ->with(['user', 'member', 'latestMessage'])
            ->withCount(['user_unseen_msg', 'member_unseen_msg'])
            ->latest('updated_at')
            ->paginate(15);

        return ApiResponse::success(
            'Chats retrieved successfully',
            ChatResource::collection($chats)
        );
    }


     public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'required|integer|exists:users,id|not_in:' . auth()->id(),
            'message'      => 'required_without:file|string|max:2000',
            'file'         => 'required_without:message|file|max:5120', // 5MB
            'listing_id'   => 'nullable|integer|exists:listings,id',
        ]);

        try {
            $newMessage = $this->chatService->sendMessage(
                auth()->id(),
                $validated['recipient_id'],
                $validated['message'] ?? '',
                $request->file('file'),
                $validated['listing_id'] ?? null
            );

            return ApiResponse::success(
                'Message sent successfully',
                new ChatMessageResource($newMessage),
                201
            );

        } catch (Exception $e) {
            Log::error('Message sending failed: ' . $e->getMessage());
            return ApiResponse::error('Failed to send message.');
        }
    }


    public function show(LiveChat $chat): JsonResponse
    {
        if (Auth::id() != $chat->user_id && Auth::id() != $chat->member_id) {
            return ApiResponse::forbidden('You are not authorized to view this chat.');
        }

        $messages = $chat->livechatMessage()
            ->with('liveChat.user', 'liveChat.member')
            ->latest()
            ->paginate(20);

        // $this->markMessagesAsSeen($chat, Auth::id());

        return ApiResponse::success(
            'Messages retrieved successfully',
            ChatMessageResource::collection($messages)
        );
    }


  public function storeMessage(Request $request, LiveChat $chat): JsonResponse
{
    if (Auth::id() != $chat->user_id && Auth::id() != $chat->member_id) {
        return ApiResponse::forbidden('You are not authorized to send messages in this chat.');
    }

    $validated = $request->validate([
        'message' => 'required_without:file|string|max:2000',
        'file'    => 'required_without:message|file|max:5120',
    ]);

    $recipientId = (Auth::id() == $chat->user_id) ? $chat->member_id : $chat->user_id;

    try {
        $message = $this->chatService->sendMessage(
            Auth::id(),
            $recipientId,
            $validated['message'] ?? '',
            $request->file('file')
        );

        return ApiResponse::success('Message sent successfully', new ChatMessageResource($message), 201);

    } catch (\Exception $e) {
        Log::error('Message sending error: ' . $e->getMessage());
        return ApiResponse::error('Failed to send message.');
    }
}

  public function markAsSeen(Request $request, LiveChat $chat, LiveChatMessage $message): JsonResponse
{
    if (Auth::id() != $chat->user_id && Auth::id() != $chat->member_id) {
        return ApiResponse::forbidden('You are not authorized to view this chat.');
    }

    $isRecipient = ($chat->user_id == Auth::id() && $message->from_user == 2) ||
                   ($chat->member_id == Auth::id() && $message->from_user == 1);

    if (!$isRecipient) {
        return ApiResponse::success('Message is not for the current user.');
    }

    $message->update(['is_seen' => 1]);

    // يمكنك هنا إرسال event عبر Pusher لإعلام الطرف الآخر بتحديث الحالة في الواجهة مباشرة
    // (e.g., تغيير علامة الصح الواحدة إلى علامتي صح)
    // broadcast(new MessageReadEvent($message))->toOthers();

    return ApiResponse::success('Message marked as seen.');
}

}
