<?php

namespace Modules\Chat\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Chat\app\Events\LiveChatMessageEvent;
use Modules\Chat\app\Http\Requests\FetchChatRecordRequest;
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Services\UserChatService;

class ChatController extends Controller
{
    public function live_chat()
    {
        $user_chat_list = LiveChat::with("user","member")
            ->whereHas('member')
            ->withCount("user_unseen_msg","member_unseen_msg")
            ->where("user_id", auth("web")->id())
            ->orderByDesc('user_unseen_msg_count')
            ->get();

        $arr = "";
        foreach($user_chat_list->pluck("member_id") as $id){
            $arr .= "member_id_". $id .": false,";
        }
        $arr = rtrim($arr,",");

        return view("chat::user.index",compact('user_chat_list','arr'));
    }

    public function fetch_chat_record(FetchChatRecordRequest $request){
        $data = $request->validated();

        $data = UserChatService::fetch($data["member_id"],$data["user_id"], from: 1);
        $currentUserType = "member";

        $body = view("chat::user.message-body", compact('data'))->render();
        $header = view("chat::user.message-header", compact('data'))->render();

        return response()->json([
            "body" => $body,
            "header" => $header,
            "allow_load_more" => $data->allow_load_more ?? false,
        ]);
    }

    public function message_send(Request $request){
        # check livechat configuration value are exist or not
        if(empty(env("PUSHER_APP_ID")) && empty(env("PUSHER_APP_KEY")) && empty(env("PUSHER_APP_SECRET")) && empty(env("PUSHER_HOST"))){
            return back()->with(toastr_error(__("Please configure your pusher credentials")));
        }

        //: send message for user to member
        $message_send = UserChatService::send(
            auth('web')->id(),
            $request->member_id,
            $request->message,
            1,
            $request->file,
            (int) ($request->listing_id)
        );

        // Broadcast the message event
        if ($message_send && isset($message_send['live_chat'])) {
            event(new LiveChatMessageEvent(
                'chat.message-template',
                $message_send, // The sent message data
                $message_send['live_chat'], // LiveChat instance
                'user', // sender type
                auth('web')->id(), // sender ID (user)
                $request->member_id // receiver ID (member/vendor)
            ));
        }

        if($request->from === 'chatbox'){
            return $message_send;
        }

        return redirect()->route('user.live.chat',[
            'member_id'=>$request->member_id
        ]);
    }
    
    
public function getMessageTemplate(Request $request)
{
    $message = $request->message;
    $livechat = LiveChat::find($message['live_chat_id']);
    
    return view("chat::components.user.message", [
        "data" => $livechat,
        "message" => $message
    ])->render();
}
}