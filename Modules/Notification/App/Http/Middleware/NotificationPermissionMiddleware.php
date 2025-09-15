<?php

namespace Modules\Notification\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Notification\App\Models\Notification;
use Symfony\Component\HttpFoundation\Response;

class NotificationPermissionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $notificationId = $request->route('notification') ?? $request->route('id');
        
        if ($notificationId) {
            $notification = Notification::findOrFail($notificationId);
            
            if ($notification->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'You do not have permission to access this notification'
                ], 403);
            }
        }
        
        return $next($request);
    }
}