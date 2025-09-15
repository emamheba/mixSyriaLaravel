<?php

namespace Modules\Notification\App\Http\Controllers\Backend;

use Modules\Notification\App\Http\Controllers\Controller;
use Modules\Notification\App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationSettingsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get user notification settings
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = $this->notificationService->getUserSettings(Auth::id());
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update user notification settings
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * Request body:
     * {
     *   "settings": [
     *     {
     *       "type_id": 1,
     *       "channels": ["database", "pusher"],
     *       "is_enabled": true
     *     },
     *     ...
     *   ]
     * }
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.type_id' => 'required|exists:notification_types,id',
            'settings.*.channels' => 'nullable|array',
            'settings.*.is_enabled' => 'boolean',
        ]);
        
        $this->notificationService->updateUserSettings(
            Auth::id(), 
            $request->settings
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully'
        ]);
    }
}