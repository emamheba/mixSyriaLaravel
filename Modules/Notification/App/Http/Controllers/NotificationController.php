<?php

namespace Modules\Notification\App\Http\Controllers;

use Modules\Notification\App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id(), ['limit' => 20]);
        $unreadCount = $this->notificationService->getUnreadCount(Auth::id());
        
        return view('notification::user.index', compact('notifications', 'unreadCount'));
    }

    public function markAllRead()
    {
        $count = $this->notificationService->markAllAsRead(Auth::id());
        
        return redirect()->back()->with('success', "{$count} notifications marked as read");
    }
}