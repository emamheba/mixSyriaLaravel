<?php

namespace App\Http\Controllers\Api\Frontend\User\Message;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $messages = Message::with(['sender', 'recipient', 'listing'])
            ->where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return ApiResponse::success('تم جلب الرسائل بنجاح', $messages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string'],
            'listing_id' => ['nullable', 'exists:listings,id'],
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $validated['recipient_id'],
            'listing_id' => $validated['listing_id'] ?? null,
            'message' => $validated['message'],
        ]);

        return ApiResponse::success('تم إرسال الرسالة بنجاح', $message, 201);
    }

    public function markAsRead(Request $request)
    {
        $user = Auth::user();

        $message = Message::where('id', $request->message_id)
            ->where('recipient_id', $user->id)
            ->firstOrFail();

        $message->update(['read_at' => now()]);

        return ApiResponse::success('تم قراءة الرسالة بنجاح', $message);
    }
}
