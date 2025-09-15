<?php

namespace Modules\Notification\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Notification\App\Http\Requests\MarkAsReadRequest;
use Modules\Notification\App\Http\Requests\StoreNotificationRequest;
use Modules\Notification\App\Models\Notification;
use Modules\Notification\App\Models\NotificationType;
use Modules\Notification\App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get user notifications with optional filters
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * Query parameters:
     * - unread: boolean (true/false) - Filter unread notifications
     * - type: string - Filter by notification type slug
     * - limit: integer - Number of notifications to return (default: 20)
     */
  public function index(Request $request): JsonResponse
    {
        $filters = [
            'unread' => $request->boolean('unread'), 
            'type' => $request->get('type'),
            'limit' => $request->get('limit', 20),
        ];
        
        $notifications = $this->notificationService->getUserNotifications(
            Auth::user(), 
            $filters
        );
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'meta' => [
                'total' => $notifications->count(),
                'unread_count' => $this->notificationService->getUnreadCount(Auth::user()),
            ]
        ]);
    }
    /**
     * Get specific notification details
     * 
     * @param int $id Notification ID
     * @return JsonResponse
     */
  public function show($id): JsonResponse
    {
        $notification = Auth::user()->notifications()
            ->with('type')
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'data' => $notification
        ]);
    }

    /**
     * Create a new notification (admin/system use)
     * 
     * @param StoreNotificationRequest $request
     * @return JsonResponse
     * 
     * Request body:
     * - user_id: integer (required) - ID of the user to notify
     * - type: string (required) - Notification type slug
     * - title: string (required) - Notification title
     * - message: string (required) - Notification message
     * - data: array (optional) - Additional data
     */
    public function store(StoreNotificationRequest $request): JsonResponse
    {
        // This endpoint is typically for admin/system use
        // You might want to add additional authorization checks
        
        $notification = $this->notificationService->create(
            $request->user_id,
            $request->type,
            $request->title,
            $request->message,
            $request->data
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Notification created successfully',
            'data' => $notification
        ], 201);
    }

    /**
     * Mark a notification as read
     * 
     * @param int $id Notification ID
     * @return JsonResponse
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = $this->notificationService->markAsRead($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     * 
     * @return JsonResponse
     */
   public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(Auth::user());
        
        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
            'count' => $count
        ]);
    }

    /**
     * Delete a notification
     * 
     * @param int $id Notification ID
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->notificationService->delete($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Get unread notifications count for the authenticated user
     * 
     * @return JsonResponse
     */
      public function unreadCount(): JsonResponse
    {
        // <-- التغيير هنا: نمرر Auth::user() بدلاً من Auth::id()
        $count = $this->notificationService->getUnreadCount(Auth::user());
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
    
    /**
     * Get all available notification types
     * 
     * @return JsonResponse
     */
    public function notificationTypes(): JsonResponse
    {
        $types = NotificationType::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }
}