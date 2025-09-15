<?php

namespace Modules\Notification\App\Services;

use Modules\Notification\App\Events\NotificationCreatedEvent;
use Modules\Notification\App\Events\NotificationDeletedEvent;
use Modules\Notification\App\Events\NotificationReadEvent;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Models\NotificationType;
use Modules\Notification\App\Models\UserNotificationSetting;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class NotificationService
{
    protected $pusherService;

    public function __construct(PusherNotificationService $pusherService)
    {
        $this->pusherService = $pusherService;
    }

    public function create($userId, $typeSlug, $title, $message, $data = [])
    {
        return DB::transaction(function () use ($userId, $typeSlug, $title, $message, $data) {
            $type = NotificationType::where('slug', $typeSlug)->firstOrFail();
            
            $notification = Notification::create([
                'user_id' => $userId,
                'type_id' => $type->id,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);
            
            // Check if user has enabled notifications for this type
            $userSetting = UserNotificationSetting::where('user_id', $userId)
                ->where('type_id', $type->id)
                ->first();
                
            $shouldNotify = !$userSetting || $userSetting->is_enabled;
            
            if ($shouldNotify) {
                event(new NotificationCreatedEvent($notification));
                $this->pusherService->broadcastNotification($notification);
            }
            
            return $notification;
        });
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        
        if (!$notification->isRead()) {
            $notification->markAsRead();
            
            event(new NotificationReadEvent($notification));
            $this->pusherService->broadcastNotificationRead($notification);
        }
        
        return $notification;
    }

     public function markAllAsRead(User $user)
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }


   public function getUserNotifications(User $user, array $filters = [])
    {
    
        $query = $user->notifications()
            ->with('type')
            ->orderBy('created_at', 'desc');
            
        if (!empty($filters['unread']) && $filters['unread']) {
            $query->whereNull('read_at');
        }
        
        if (!empty($filters['type'])) {
            $query->whereHas('type', function ($q) use ($filters) {
                $q->where('slug', $filters['type']);
            });
        }
        
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }
        
        return $query->get();
    }

  public function getUnreadCount(User $user)
    {
      
        return $user->unreadNotifications()->count();
    }

    public function delete($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $userId = $notification->user_id;
        $notificationId = $notification->id;
        
        $notification->delete();
        
        event(new NotificationDeletedEvent($userId, $notificationId));
        $this->pusherService->broadcastNotificationDeleted($userId, $notificationId);
        
        return true;
    }
    
    public function getUserSettings($userId)
    {
        return UserNotificationSetting::with('type')
            ->where('user_id', $userId)
            ->get();
    }
    
    public function updateUserSettings($userId, $settings)
    {
        return DB::transaction(function () use ($userId, $settings) {
            foreach ($settings as $setting) {
                UserNotificationSetting::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'type_id' => $setting['type_id'],
                    ],
                    [
                        'channels' => $setting['channels'] ?? null,
                        'is_enabled' => $setting['is_enabled'] ?? true,
                    ]
                );
            }
            
            return true;
        });
    }
}