<?php

namespace Modules\Chat\app\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JetBrains\PhpStorm\NoReturn;
use Modules\Chat\app\Events\LiveChatMessageEvent;
use Modules\Chat\app\Http\Requests\FetchChatRecordRequest;
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Services\UserChatService;

class MemberChatController extends Controller
{
    public function live_chat()
    {
        $member_chat_list = LiveChat::with("user","member")
            ->whereHas('user')
            ->withCount("user_unseen_msg","member_unseen_msg")
            ->where("member_id", auth("web")->id())
            ->orderByDesc('member_unseen_msg_count')
            ->get();

        $arr = "";
        foreach($member_chat_list->pluck("user_id") as $id){
            $arr .= "user_id_". $id .": false,";
        }

        $arr = rtrim($arr,",");
        return view("chat::member.index",compact('member_chat_list','arr'));
    }

    public function fetch_chat_record(FetchChatRecordRequest $request){
        $data = $request->validated();
        $data = UserChatService::fetch($data["member_id"],$data["user_id"], from: 2);

        $body = view("chat::member.message-body", compact('data'))->render();
        $header = view("chat::member.message-header", compact('data'))->render();

        return response()->json([
            "body" => $body,
            "header" => $header,
            "allow_load_more" => $data->allow_load_more ?? false,
        ]);
    }

    /**
     * @throws Exception
     */
    public function message_send(Request $request){
        if(empty(config("broadcasting.connections.app_id")) && empty(config("broadcasting.connections.pusher.key")) && empty(config("broadcasting.connections.pusher.secret")) && empty(config("broadcasting.connections.pusher.options.cluster"))){
            return back()->with(toastr_error(__("Please configure your pusher credentials")));
        }

        //: send message
        $message_send = UserChatService::send(
            $request->user_id,
            auth('web')->id(),
            $request->message,
            2,
            $request->file,
            $request->listing_id ?? null
        );

        // Broadcast the message event
        if ($message_send && isset($message_send['live_chat'])) {
            event(new LiveChatMessageEvent(
                'chat.message-template',
                $message_send, // The sent message data
                $message_send['live_chat'], // LiveChat instance
                'vendor', // sender type (vendor/member)
                auth('web')->id(), // sender ID (member/vendor)
                $request->user_id // receiver ID (user)
            ));
        }

        if($request->from === 'chatbox'){
            return $message_send;
        }

        return redirect()->route('member.live.chat',[
            'user_id'=>$request->user_id
        ]);
    }
    
public function getMessageTemplate(Request $request)
{
    $message = $request->message;
    $livechat = LiveChat::find($message['live_chat_id']);
    
    return view("chat::components.member.message", [
        "data" => $livechat,
        "message" => $message
    ])->render();
}

}