<?php

namespace Modules\Chat\app\Services;

use App\Models\Backend\Listing;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Intervention\Image\Facades\Image;
use InvalidArgumentException;
use JetBrains\PhpStorm\NoReturn;
use Modules\Chat\app\Events\LiveChatMessageEvent; // Updated event
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Models\LiveChatMessage;

class UserChatService
{
    private $liveChat = null;
    private $livechatQuery = null;
    private $lastMessage = null;
    private $projectData = null;
    private $clientData = null;
    private array $message = [];
    private string $filename = '';
    private $instance;
    private array $allowedFilesExtension = ['jpeg','jpg','png','pdf','gif','webp'];
    public const FOLDER_PATH = 'assets/uploads/media-uploader/live-chat/';

    private static function init(): UserChatService
    {
        $init = new self();
        if(is_null($init->instance)){
            $init->instance = $init;
        }
        return $init;
    }

    private function recordIsExistsOrNot($userId, $memberId){
        $this->livechatQuery = LiveChat::where("user_id", $userId)->where("member_id",$memberId);
        return $this->livechatQuery->count();
    }

    private function livechatInstance(int $userId, int $memberId){
        if($this->recordIsExistsOrNot($userId, $memberId) > 0){
            return $this->livechatQuery->first();
        }
        return LiveChat::create([
            "user_id" => $userId,
            "member_id" => $memberId
        ]);
    }

    //  create new livechat message record
    private function sendMessage(array|object $data){
        $this->lastMessage = LiveChatMessage::create($data);

        return $this->lastMessage;
    }

    private function fetch_member_info($member_id){
        if(gettype($member_id) == 'integer'){
            $this->userData = User::select("id","image","first_name","last_name")->find($member_id);
            return $this->userData;
        }

        if(is_null($member_id)){
            return null;
        }

        //  now throw exception
        throw new InvalidArgumentException("Invalid member id this id should be integer " . gettype($member_id) . ' given at line:'. __LINE__ . " File: ". __FILE__);
    }

    private function fetch_client_info(?int $user_id)
    {
        if(gettype($user_id) == 'integer'){
            $this->clientData = User::select("id","image","first_name","last_name")->find($user_id);
            return $this->clientData;
        }

        if(is_null($user_id)){
            return null;
        }

        //  now throw exception
        throw new InvalidArgumentException("Invalid client id this id should be integer " . gettype($user_id) . ' given at line:'. __LINE__ . " File: ". __FILE__);
    }

    private function updateUnSeen($livechat_id, $type): void
    {
        LiveChatMessage::where("live_chat_id", $livechat_id)
            ->when($type == 1, function ($query) {
                $query->where("from_user", 2);
            })->when($type == 2, function ($query) {
                $query->where("from_user", 1);
            })->update([
                "is_seen" => 1
            ]);
    }

    public static function fetch(int $member_id,int $user_id,$from,int|string $type = 'all',int $limit = 20): LiveChat
    {
        $data = null;
        $instance = self::init();
        $livechat = LiveChat::where("member_id", $member_id)->where("user_id", $user_id)->first();
        $instance->updateUnSeen($livechat->id,$from);
        $liveChatMessages = LiveChatMessage::where("live_chat_id", $livechat->id)
            ->when($type == 0, function ($query){
                $query->where("is_seen", 0);
            })
            ->latest('id')->paginate($limit);

        $liveChatMessages = $liveChatMessages->reverse();
        $livechat->pagination = $liveChatMessages;
        $livechat->messages = $liveChatMessages;
        $livechat->member = $instance->fetch_member_info($livechat->member_id);
        $livechat->user = $instance->fetch_client_info($livechat->user_id);
        $livechat->allow_load_more = LiveChatMessage::where("live_chat_id", $livechat->id)
            ->when($type == 0, function ($query){
                $query->where("is_seen", 0);
            })->count() > $limit;

        return $livechat;
    }

    /**
     * @throws Exception
     */
    #[NoReturn]
    public static function send(int $user_id,int $member_id,?string $message,int $messageFrom,$file = null,?int $listing_id = null, $responseType = 'html'): View|Factory|array|string|Application|null
    {

        $instance = self::init();
        $instance->liveChat = $instance->livechatInstance($user_id, $member_id);
        $instance->message["message"] = $message;
        $instance->message["listing"] = $listing_id ? $instance->prepareProductDetails($listing_id) : null;
        if(!empty($file)){
            $instance->storeFile($file);
        }
        $message = $instance->storeMessage($messageFrom);
        $instance->fireEvent($message, $instance->liveChat, $messageFrom);
        return $instance->sendResponse($message, $instance->liveChat, $messageFrom, $responseType);
    }

    private function sendResponse($message, $livechat, $messageFrom, $responseType){
        if($responseType == 'json'){
            return [
                "message" => $message,
                "livechat" => $livechat
            ];
        }
        if($messageFrom == 2){
            return view("chat::components.member.message", [
                "data" => $livechat,
                "message" => $message
            ])->render();
        }elseif($messageFrom == 1){
            return view("chat::components.user.message", [
                "message" => $message,
                "data" => $livechat,
            ]);
        }
    }

    public function fireEvent($message, $livechat, $messageFrom): void
    {
        // Determine sender type based on messageFrom
        $senderType = ($messageFrom == 1) ? 'user' : 'vendor';
        
        // Determine sender and receiver IDs
        $senderId = ($messageFrom == 1) ? $livechat->user_id : $livechat->member_id;
        $receiverId = ($messageFrom == 1) ? $livechat->member_id : $livechat->user_id;

        // Get user images for the blade template
        $user_image = $livechat->user?->image;
        $member_image = $livechat->member?->image;

        // Render the appropriate blade template
        if ($messageFrom == 2) {
            $messageBlade = view("chat::components.user.message", [
                "data" => $livechat,
                "message" => $message,
                "userImage" => $user_image,
                "memberImage" => $member_image
            ])->render();
        } else {
            $messageBlade = view("chat::components.member.message", [
                "data" => $livechat,
                "message" => $message,
                "userImage" => $user_image,
                "memberImage" => $member_image
            ])->render();
        }

        // Fire the consolidated event
        event(new LiveChatMessageEvent(
            $messageBlade,
            $message,
            $livechat,
            $senderType,
            $senderId,
            $receiverId
        ));
    }

    private function storeMessage(int $from_user): LiveChatMessage
    {
        return LiveChatMessage::create([
            'live_chat_id' => $this->liveChat?->id,
            'message' => $this->message,
            'file' => $this->filename,
            'from_user' => $from_user,
            'is_seen' => 0
        ]);
    }

    /**
     * @throws Exception
     */
    private function storeFile($file) : void
    {
        $extension = $file->extension();
        // Check if the file extension is allowed
        if (!in_array($extension, $this->allowedFilesExtension)) {
            throw new Exception('The file you have uploaded with '. $extension .' extension are not allowed.');
        }
        $filename = time() . rand(111111,999999) . '.' . $extension;

       // image check
        $extensions = array('png','jpg','jpeg','gif', 'webp','svg');
        if(in_array($extension, $extensions)){
            $resize_full_image = Image::make($file)
                ->resize(300, 300);
            $resize_full_image->save(self::FOLDER_PATH .'/'. $filename);
        }else{
            $file->move(self::FOLDER_PATH, $filename);
        }

        $this->filename = $filename;
    }

    private function prepareProductDetails($listing_id): array {
        $listing = $this->getProductDetails($listing_id);
        return [
            'id' => $listing->id,
            'listing_creator' => $listing->listing_creator?->user_id,
            'username' => $listing->listing_creator?->username,
            'title' => $listing->title,
            'slug' => $listing->slug,
            'image' => $listing->image
        ];
    }

    private function getProductDetails($listing_id)
    {
        if(!is_null($listing_id) && (gettype($listing_id) == 'integer')){
            $this->projectData = Listing::select("id","title","slug","image",'user_id')->with('listing_creator')->find($listing_id);
            return $this->projectData;
        }
        if(is_null($listing_id)){
            return null;
        }

        //  now throw exception
        throw new InvalidArgumentException("Invalid id. This id should be integer" . gettype($listing_id) . ' given at line:' . __LINE__ . ' File: '. __FILE__);
    }
}