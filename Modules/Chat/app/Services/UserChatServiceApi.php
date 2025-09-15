<?php

namespace Modules\Chat\app\Services;

use App\Models\Backend\Listing;
use App\Models\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager; 
use Intervention\Image\Drivers\Gd\Driver; 
use Modules\Chat\app\Events\NewChatMessageEvent; // Use the correct event
use Modules\Chat\app\Models\LiveChat;
use Modules\Chat\app\Models\LiveChatMessage;

class UserChatServiceApi
{
    private const FOLDER_PATH = 'live-chat'; 
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
    private const ALLOWED_FILE_EXTENSIONS = ['pdf', 'doc', 'docx','xlsx'];
    private $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

 public function sendMessage(
    int $senderId,
    int $recipientId,
    string $messageText,
    ?UploadedFile $file = null
): LiveChatMessage
{
    $chat = $this->findOrCreateChat($senderId, $recipientId);
    $filePath = $file ? $this->storeFile($file) : null;

    // Determine from_user value based on who is sending
    $fromUser = ($chat->user_id === $senderId) ? 1 : 2;

    $message = $chat->livechatMessage()->create([
        'message'   => ['text' => $messageText], 
        'file'      => $filePath,
        'from_user' => $fromUser,
        'is_seen'   => 0
    ]);

    $chat->touch();

    try {
        // Load relationships for broadcasting - THIS IS CRITICAL
        $message->load(['liveChat.user', 'liveChat.member']);
        
        // Use the correct NewChatMessageEvent for API
        broadcast(new NewChatMessageEvent($message))->toOthers();

    } catch (Exception $e) {
        Log::error('Pusher broadcast failed: ' . $e->getMessage());
    }

    return $message;
}   
    // public function sendMessage(
    //     int $senderId,
    //     int $recipientId,
    //     string $messageText,
    //     ?UploadedFile $file = null
    // ): LiveChatMessage
    // {
    //     $chat = $this->findOrCreateChat($senderId, $recipientId);
    //     $filePath = $file ? $this->storeFile($file) : null;

    //     // Determine from_user value based on who is sending
    //     $fromUser = ($chat->user_id === $senderId) ? 1 : 2;

    //     $message = $chat->livechatMessage()->create([
    //         'message'   => ['text' => $messageText], 
    //         'file'      => $filePath,
    //         'from_user' => $fromUser,
    //         'is_seen'   => 0
    //     ]);

    //     $chat->touch();

    //     try {
    //         // Load relationships for broadcasting
    //         $message->load(['liveChat.user', 'liveChat.member']);
            
    //         // Use the correct NewChatMessageEvent for API
    //         broadcast(new NewChatMessageEvent($message))->toOthers();

    //     } catch (Exception $e) {
    //         Log::error('Pusher broadcast failed: ' . $e->getMessage());
    //     }

    //     return $message;
    // }

    
    protected function findOrCreateChat(int $userOneId, int $userTwoId): LiveChat
    {
        $participants = [$userOneId, $userTwoId];
        sort($participants);

        return LiveChat::firstOrCreate([
            'user_id'   => $participants[0],
            'member_id' => $participants[1],
        ]);
    }

    protected function storeFile(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = array_merge(self::ALLOWED_IMAGE_EXTENSIONS, self::ALLOWED_FILE_EXTENSIONS);

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("File type '{$extension}' is not allowed.");
        }

        $filename = uniqid('chat_', true) . '.' . $extension;
        $path = self::FOLDER_PATH . '/' . $filename;

        if (in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS)) {
            $image = $this->imageManager->read($file)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            Storage::disk('public')->put($path, $image->encode());
        } else {
            Storage::disk('public')->putFileAs(self::FOLDER_PATH, $file, $filename);
        }

        return $path;
    }

  
    protected function getListingDetails(?int $listingId): ?array
    {
        if (!$listingId) {
            return null;
        }

        $listing = Listing::with('user:id,first_name,last_name,username')
            ->select('id', 'title', 'slug', 'image', 'user_id')
            ->find($listingId);

        if (!$listing) {
            return null;
        }

        return [
            'id'    => $listing->id,
            'title' => $listing->title,
            'slug'  => $listing->slug,
            'image' => $listing->image_url,
            'user'  => $listing->user ? [
                'id' => $listing->user->id,
                'username' => $listing->user->username
            ] : null
        ];
    }
}